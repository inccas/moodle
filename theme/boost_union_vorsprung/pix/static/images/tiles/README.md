# Tile images

## Known course ids

| Name                                   |   id   |
| ====================================== | ====== |
| Fachkurs Informatik                    |   15   |
| Fachkurs Mathematik                    |   16   |
| Fachkurs Physik                        |   18   |
| Fachkurs Chemie                        |   19   |
| Vorkurs Mathematik                     |   33   |
| Campus                                 |   28   |

## Medienserver

* Basefolder: `/static/images/tiles`

### Main tiles (LE 1, LE 2, ...)

* Filename pattern: `unittile-<courseid>-<unitid>.png`  
  Example: 
  * Course: Fachkurs Physik -> `courseid` = 18
  * Unit: LE 4 Kreisförmige Bewegung -> `unitid` = 4
  * Tile image: `/static/images/tiles/unittile-18-4.png`
* Titles  
  Source code will automatically add a line break after LE x.  
  Example:  
  Input:  
  `LE 4 Kreisförmige Bewegung`  
  Output:
  ```
  LE 4
  Kreisförmige Bewegung
  ```

### Subtiles

not implemented yet...