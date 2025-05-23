<?php

/**
 * Implementation of zip file archive, using external zip utilities
 *
 * @package    local/externalzip
 * @copyright  2012 Aaron Wells <aaronw@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/filestorage/file_archive.php");

/**
 * zip file archive class. Works by unzipping the entire zip file into a temp directory, and just
 * zipping it up when you "close" this archive. If you just wanted to add or fetch a single file,
 * it would be more efficient to use the zip utility to add/fetch that single file directly. But
 * in practice, Moodle never does that. It's always creating an archive from scratch, or extracting
 * an archive in its entirety.
 *
 * @package    local/externalzip
 * @copyright  2012 Aaron Wells <aaronw@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class externalzip_archive extends file_archive {

    /** Constants used by $this->exec() to indicate which executable to use */
    const ZIP = 1;
    const UNZIP = 2;

    /**
     * Pathname of physical zip file we're editing. Due to constraints of the zip utility,
     * this will have ".zip" on the end of it
     * @var string
     */
    protected $pathtozipfile = null;

    /**
     * Directory the zip file is located in
     * @var string
     */
    protected $dirofzipfile = null;

    /**
     * A list of all the files in the archive. This will be an array
     * of info objects like those returned by get_info().
     */
    protected $filelist = null;

    /** Iteration position */
    protected $pos = 0;

    /** Indicates whether or not the script is operating in a Windows environment */
    protected $iswindows = false;

    /** Indicates what string to use to separate two commands at the command line */
    protected $cmdseparator = '';

    /** Indicates what to use to redirect errors to standard IO */
    protected $redirection = '';

    /** Mode this archive was opened in (OPEN, CREATE, OVERWRITE) */
    protected $mode = file_archive::CREATE;

    /** File encoding to use */
    protected $encoding = 'utf-8';

    /**
     * A temp directory used specifically for operations on this file. Deleted when you
     * "close" this archive
     * @var string
     */
    protected $tempdir;
    protected $archivetempdir;

    /**
     * Whether or not we've updated the archive contents
     * @var boolean
     */
    private $archiveupdated = false;

    /**
     * Open or create archive (depending on $mode)
     * @param string $pathtozipfile
     * @param int $mode OPEN, CREATE or OVERWRITE constant
     * @param string $encoding archive local paths encoding
     * @return bool success
     */
    public function open($pathtozipfile, $mode=file_archive::CREATE, $encoding='utf-8') {
        global $CFG;

        $this->iswindows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
        $this->cmdseparator = $this->iswindows ? ' &' : ' ;';
        $this->redirection = $this->iswindows ? '' : ' 2>&1';

        $this->mode = $mode;
        $this->encoding = $encoding;

        $alreadyexists = file_exists($pathtozipfile);

        // If it's an existing file, then use realpath() to clean up the path
        if ($alreadyexists) {
            $this->pathtozipfile = realpath($pathtozipfile);
        } else {
            $this->pathtozipfile = $pathtozipfile;
        }

        $this->dirofzipfile = dirname($this->pathtozipfile);

        // If it's an existing file, then check if it's writeable.
        if ($alreadyexists) {
            // ... and a directory is not a zip file, so just quit here if it's a directory
            if (is_dir($this->pathtozipfile)) {
                return false;
            }
            $iswritable = is_writeable($this->pathtozipfile);
        } else {
            $iswritable = is_writeable($this->dirofzipfile);
        }

        $this->mode = $mode;

        // Create a temp directory to unzip files into
        $tempdir = 'local_externalzip/'.random_string(20);
        $this->tempdir = make_temp_directory($tempdir);
        $this->archivetempdir = make_temp_directory($tempdir.'/archive');

        switch($mode) {
            // Open archive if it exists, fail if it does not
            case file_archive::OPEN:
                if (!$alreadyexists) {
                    return false;
                }
                $this->extract_zipfile();
                break;

            // Open archive if it exists, create if it does not
            case file_archive::CREATE:
                if (!$iswritable) {
                    return false;
                }
                if ($alreadyexists) {
                    $this->extract_zipfile();
                } else {
                    $this->filelist = array();
                }
                break;

            // Overwrite archive, whether it exists or not
            case file_archive::OVERWRITE:
                if (!$iswritable) {
                    return false;
                }
                $this->filelist = array();
                break;

            default :
                debugging('invalid archive mode provided to externalzip_archive::open');
                return false;
        }

        return true;
    }

    /**
     * Close archive
     * @return bool success
     */
    public function close() {
        if ($this->archiveupdated) {
            // The zip utility will automatically add the ".zip" file extension to the generated file
            // if the file has no file extension. To get around this, we'll always put .zip on the
            // end ourselves and then remove it afterwards.
            $zipname = "{$this->tempdir}/archive.zip";
            list($output, $exitval) = $this->exec(
                    externalzip_archive::ZIP,
                    ' -r -q ' . escapeshellarg($zipname) . ' ./ ',
                    $this->archivetempdir
            );
            rename($zipname, $this->pathtozipfile);
        }

        // Deallocate the file list
        unset($this->filelist);

        // Delete the temp directory
        return remove_dir($this->tempdir, false);
    }

    /**
     * Returns a file stream to read the file at specified index.
     * We implement this by unzipping the file into the read temp directory, and
     * fopening it and returning that fopen. Note it returns the stream in "r"
     * mode, since that's what zip_archive does.
     * @param int $index of file
     * @return filepointer in "r" mode
     */
    public function get_stream($index) {
        if (!isset($this->filelist)) {
            return false;
        }

        $info = $this->get_info($index);
        $fp = fopen("{$this->archivetempdir}/{$info->pathname}", 'r');

        return $fp;
    }

    /**
     * Returns file information
     * @param int $index of file
     * @return info object or false if error
     */
    public function get_info($index) {
        if (!isset($this->filelist)) {
            return false;
        }

        if ($index < 0 or $index >=$this->count()) {
            return false;
        }

        return $this->filelist[$index];
    }

    /**
     * Returns array of info about all files in archive
     * @return array of file infos
     */
    public function list_files() {
        if (!isset($this->filelist)) {
            return false;
        }

        return $this->filelist();
    }

    /**
     * Returns number of files in archive
     * @return int number of files
     */
    public function count() {
        if (!isset($this->filelist)) {
            return false;
        }

        return count($this->filelist);
    }

    /**
     * Add file into archive.
     * @param string $pathinarchive Desired destination path of file in the zip archive (null to just copy $sourcepath)
     * @param string $sourcepath The physical source file to add to the archive
     * @return bool success
     */
    public function add_file_from_pathname($pathinarchive, $sourcepath) {
        if ($this->pathtozipfile === realpath($sourcepath)) {
            // do not add self into archive
            return false;
        }
        if (is_null($pathinarchive)) {
            $pathinarchive = clean_param($sourcepath, PARAM_PATH);
        }

        $pathinarchive = $this->clean_addfile_path($pathinarchive);
        if (false === $pathinarchive) {
            return false;
        }

        // Just copy the file into the requested path under $this->archivetempdir
        $destpath = "{$this->archivetempdir}/{$pathinarchive}";
        $result =
            check_dir_exists(dirname($destpath))
            && copy($sourcepath, $destpath);

        if ($result) {
            $this->archiveupdated = true;

            $info = new stdClass();
            $info->original_pathname = $info->pathname = $pathinarchive;
            $info->mtime = time();
            $info->is_directory = false;
            $info->size = filesize($sourcepath);
            $info->index = count($this->filelist);
            $this->filelist[$info->index] = $info;
        }

        return $result;
    }

    /**
     * Add content of string into archive
     * @param string $localname name of file in archive
     * @param string $contents
     * @return bool success
     */
    public function add_file_from_string($pathinarchive, $contents) {

        $pathinarchive = $this->clean_addfile_path($pathinarchive);
        if (false === $pathinarchive) {
            return false;
        }

        // Write the content directly into a file at the requested path under $this->archivetempdir
        $destfile = "{$this->archivetempdir}/{$pathinarchive}";
        $result =
            check_dir_exists(dirname($destfile))
            && file_put_contents($destfile, $contents);

        if ($result) {
            $this->archiveupdated = true;

            $info = new stdClass();
            $info->original_pathname = $info->pathname = $pathinarchive;
            $info->mtime = time();
            $info->is_directory = false;
            $info->size = filesize($destfile);
            $info->index = count($this->filelist);
            $this->filelist[$info->index] = $info;
        }

        return $result;
    }

    /**
     * Create empty directory in archive
     * @param string $local
     * @return bool success
     */
    public function add_directory($pathinarchive) {
        $pathinarchive = $this->clean_addfile_path($pathinarchive);
        if (false === $pathinarchive) {
            return false;
        }

        // Just create a directory under $this->archivetempdir
        $result = check_dir_exists("{$this->archivetempdir}/{$pathinarchive}");

        if ($result) {
            $this->archiveupdated = true;

            $info = new stdClass();
            $info->original_pathname = $info->pathname = $pathinarchive;
            $info->mtime = time();
            $info->is_directory = true;
            $info->size = 0;
            $info->index = count($this->filelist);
            $this->filelist[$info->index] = $info;
        }

        return $result;
    }


    /**
     * Cleans the pathname of a file/directory being created/added in the archive
     * @param string $pathinarchive
     * @return boolean|string false if it failed, the cleaned path otherwise
     */
    private function clean_addfile_path($pathinarchive) {
        $pathinarchive = trim($pathinarchive, '/');
        $pathinarchive = $this->mangle_pathname($pathinarchive);
        if ($pathinarchive === '') {
            return false;
        } else {
            return $pathinarchive;
        }
    }

    /**
     * Returns current file info
     * @return object
     */
    public function current() {
        if (!isset($this->filelist)) {
            return false;
        }

        return $this->get_info($this->pos);
    }

    /**
     * Returns the index of current file
     * @return int current file index
     */
    public function key() {
        return $this->pos;
    }

    /**
     * Moves forward to next file
     * @return void
     */
    public function next() {
        $this->pos++;
    }

    /**
     * Rewinds back to the first file
     * @return void
     */
    public function rewind() {
        $this->pos = 0;
    }

    /**
     * Did we reach the end?
     * @return boolean
     */
    public function valid() {
        if (!isset($this->filelist)) {
            return false;
        }

        return ($this->pos < $this->count());
    }

    /**
     * Loads information on all files in the zip archive, into $this->filelist
     * @return boolean
     */
    private function extract_zipfile() {

        // First, read all the info about the file from unzip -l
        // This is easier to parse than ls -R and it's probably more cross-platform compatible
        $this->filelist = array();

        list($rawoutput, $exitval) = $this->exec(externalzip_archive::UNZIP, '-l ' . escapeshellarg($this->pathtozipfile), false, false);
        if ($exitval !== 0) {
            return;
        }

        // TODO: Is the output different on Windows or even other Linux platforms?
        // I tested it only on Ubuntu.

        // The first three lines of output will be headers, and the last 2 lines will be a summary
        // So, we can skip those.
        for ($i=3, $j=0; $i<=count($rawoutput)-2; $i++, $j++) {
            // Need to get:
            // 1. original_pathname: pathname in original encoding
            // 2. pathname (in utf8 encoding)
            // 3. mtime
            // 4. is_directory
            // 5. size
            $columns = preg_split("/[\s,]+/", trim($rawoutput[$i]));

            // Each line of output should be like this:
            // 66667  2012-05-10 10:21   moodle.xml

            // TODO: More robust testing that the line is in the expected format?
            if (count($columns) != 4) {
                continue;
            }
            $info = new stdClass();
            $info->original_pathname = $columns[3];
            $info->pathname = $this->unmangle_pathname($info->original_pathname);
            $info->mtime = strtotime($columns[1].' '.$columns[2]);
            $info->is_directory = (substr($info->pathname, -1) == '/');
            $info->size = (int) $columns[0];
            $info->index = $j;
            $this->filelist[$j] = $info;
        }

        // Now, unzip the file
        $this->exec(externalzip_archive::UNZIP, escapeshellarg($this->pathtozipfile) . " -d {$this->archivetempdir}");
    }

    /**
     * Execute a zip-related shell command
     * @param int $executable ZIP or UNZIP constant
     * @param string $arguments Which should already have had its contents passed through
     *     escapeshellarg(), where appropriate
     * @param mixed $cdpath A path to CD to before executing the command, or false if you needn't bother with a cd
     * @param boolean $errorstostdout
     * @return array Item 0 is an array with each line of output, item 1 is the exit value of the command
     */
    private function exec($executable, $arguments, $cdpath = false, $errorstostdout = true) {
        global $CFG;

        switch ($executable) {
            case externalzip_archive::ZIP:
                $executable = get_config('local_externalzip', 'zip');
                // Make sure zip uses our tempdir (under Moodle's dataroot)
                // as the temp directory for zipping
                $arguments = '-b ' . escapeshellarg($this->tempdir) . ' ' . $arguments;
                break;
            case externalzip_archive::UNZIP:
                $executable = get_config('local_externalzip', 'unzip');
                break;
            default:
                throw new Exception('Invalid executable to externalzip_archive::exec');
        }

        $command = escapeshellarg($executable) .' '. $arguments;
        if ($cdpath) {
            if ($this->iswindows) {
                $chdir = 'dir ';
            } else {
                $chdir = 'cd ';
            }
            $command = $chdir . escapeshellarg($cdpath) . " {$this->cmdseparator} {$command}";
        }
        if ($errorstostdout) {
            $command .= $this->redirection;
        }
        if ($this->iswindows) {
            $command = str_replace('/', '\\', $command);
        }
        exec($command, $list, $exitval);
        return array($list, $exitval);
    }
}
