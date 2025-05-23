c.prototype = {
    constructor: c,
    options: {},
    _id: 0,
    _trigger: function(b, c) {
        c = c || {};
        this.element.trigger(a.extend({
            type: b,
            iconpickerInstance: this
        }, c));
    },
    _createPopover: function() {
        this.popover = a(this.options.templates.popover);
        var c = this.popover.find(".popover-title");
        if (!!this.options.title) {
            c.append(a('<div class="popover-title-text">' + this.options.title + "</div>"));
        }
        if (this.hasSeparatedSearchInput() && !this.options.searchInFooter) {
            c.append(this.options.templates.search);
        } else if (!this.options.title) {
            c.remove();
        }
        if (this.options.showFooter && !b.isEmpty(this.options.templates.footer)) {
            var d = a(this.options.templates.footer);
            if (this.hasSeparatedSearchInput() && this.options.searchInFooter) {
                d.append(a(this.options.templates.search));
            }
            if (!b.isEmpty(this.options.templates.buttons)) {
                d.append(a(this.options.templates.buttons));
            }
            this.popover.append(d);
        }
        if (this.options.animation === true) {
            this.popover.addClass("fade");
        }
        return this.popover;
    },
    _createIconpicker: function() {
        var b = this;
        this.iconpicker = a(this.options.templates.iconpicker);
        var c = function(c) {
            var d = a(this);
            if (d.is("i")) {
                d = d.parent();
            }
            b._trigger("iconpickerSelect", {
                iconpickerItem: d,
                iconpickerValue: b.iconpickerValue
            });
            if (b.options.mustAccept === false) {
                b.update(d.data("iconpickerValue"));
                b._trigger("iconpickerSelected", {
                    iconpickerItem: this,
                    iconpickerValue: b.iconpickerValue
                });
            } else {
                b.update(d.data("iconpickerValue"), true);
            }
            if (b.options.hideOnSelect && b.options.mustAccept === false) {
                b.hide();
            }
            c.preventDefault();
            return false;
        };
        for (var d in this.options.icons) {
            if (typeof this.options.icons[d] === "string") {
                var e = a(this.options.templates.iconpickerItem);
                e.find("i").addClass(this.options.fullClassFormatter(this.options.icons[d]));
                e.data("iconpickerValue", this.options.icons[d]).on("click.iconpicker", c);
                this.iconpicker.find(".iconpicker-items").append(e.attr("title", "." + this.options.icons[d]));
            }
        }
        this.popover.find(".popover-content").append(this.iconpicker);
        return this.iconpicker;
    },
    _isEventInsideIconpicker: function(b) {
        var c = a(b.target);
        if ((!c.hasClass("iconpicker-element") || c.hasClass("iconpicker-element") && !c.is(this.element)) && c.parents(".iconpicker-popover").length === 0) {
            return false;
        }
        return true;
    },
    _bindElementEvents: function() {
        var c = this;
        this.getSearchInput().on("keyup.iconpicker", function() {
            c.filter(a(this).val().toLowerCase());
        });
        this.getAcceptButton().on("click.iconpicker", function() {
            var a = c.iconpicker.find(".iconpicker-selected").get(0);
            c.update(c.iconpickerValue);
            c._trigger("iconpickerSelected", {
                iconpickerItem: a,
                iconpickerValue: c.iconpickerValue
            });
            if (!c.isInline()) {
                c.hide();
            }
        });
        this.getCancelButton().on("click.iconpicker", function() {
            if (!c.isInline()) {
                c.hide();
            }
        });
        this.element.on("focus.iconpicker", function(a) {
            c.show();
            a.stopPropagation();
        });
        if (this.hasComponent()) {
            this.component.on("click.iconpicker", function() {
                c.toggle();
            });
        }
        if (this.hasInput()) {
            this.input.on("keyup.iconpicker", function(d) {
                if (!b.inArray(d.keyCode, [ 38, 40, 37, 39, 16, 17, 18, 9, 8, 91, 93, 20, 46, 186, 190, 46, 78, 188, 44, 86 ])) {
                    c.update();
                } else {
                    c._updateFormGroupStatus(c.getValid(this.value) !== false);
                }
                if (c.options.inputSearch === true) {
                    c.filter(a(this).val().toLowerCase());
                }
            });
        }
    },
    _bindWindowEvents: function() {
        var b = a(window.document);
        var c = this;
        var d = ".iconpicker.inst" + this._id;
        a(window).on("resize.iconpicker" + d + " orientationchange.iconpicker" + d, function(a) {
            if (c.popover.hasClass("in")) {
                c.updatePlacement();
            }
        });
        if (!c.isInline()) {
            b.on("mouseup" + d, function(a) {
                if (!c._isEventInsideIconpicker(a) && !c.isInline()) {
                    c.hide();
                }
                a.stopPropagation();
                a.preventDefault();
                return false;
            });
        }
        return false;
    },
    _unbindElementEvents: function() {
        this.popover.off(".iconpicker");
        this.element.off(".iconpicker");
        if (this.hasInput()) {
            this.input.off(".iconpicker");
        }
        if (this.hasComponent()) {
            this.component.off(".iconpicker");
        }
        if (this.hasContainer()) {
            this.container.off(".iconpicker");
        }
    },
    _unbindWindowEvents: function() {
        a(window).off(".iconpicker.inst" + this._id);
        a(window.document).off(".iconpicker.inst" + this._id);
    },
    updatePlacement: function(b, c) {
        b = b || this.options.placement;
        this.options.placement = b;
        c = c || this.options.collision;
        c = c === true ? "flip" : c;
        var d = {
            at: "right bottom",
            my: "right top",
            of: this.hasInput() && !this.isInputGroup() ? this.input : this.container,
            collision: c === true ? "flip" : c,
            within: window
        };
        this.popover.removeClass("inline topLeftCorner topLeft top topRight topRightCorner " + "rightTop right rightBottom bottomRight bottomRightCorner " + "bottom bottomLeft bottomLeftCorner leftBottom left leftTop");
        if (typeof b === "object") {
            return this.popover.pos(a.extend({}, d, b));
        }
        switch (b) {
            case "inline":
            {
                d = false;
            }
                break;

            case "topLeftCorner":
            {
                d.my = "right bottom";
                d.at = "left top";
            }
                break;

            case "topLeft":
            {
                d.my = "left bottom";
                d.at = "left top";
            }
                break;

            case "top":
            {
                d.my = "center bottom";
                d.at = "center top";
            }
                break;

            case "topRight":
            {
                d.my = "right bottom";
                d.at = "right top";
            }
                break;

            case "topRightCorner":
            {
                d.my = "left bottom";
                d.at = "right top";
            }
                break;

            case "rightTop":
            {
                d.my = "left bottom";
                d.at = "right center";
            }
                break;

            case "right":
            {
                d.my = "left center";
                d.at = "right center";
            }
                break;

            case "rightBottom":
            {
                d.my = "left top";
                d.at = "right center";
            }
                break;

            case "bottomRightCorner":
            {
                d.my = "left top";
                d.at = "right bottom";
            }
                break;

            case "bottomRight":
            {
                d.my = "right top";
                d.at = "right bottom";
            }
                break;

            case "bottom":
            {
                d.my = "center top";
                d.at = "center bottom";
            }
                break;

            case "bottomLeft":
            {
                d.my = "left top";
                d.at = "left bottom";
            }
                break;

            case "bottomLeftCorner":
            {
                d.my = "right top";
                d.at = "left bottom";
            }
                break;

            case "leftBottom":
            {
                d.my = "right top";
                d.at = "left center";
            }
                break;

            case "left":
            {
                d.my = "right center";
                d.at = "left center";
            }
                break;

            case "leftTop":
            {
                d.my = "right bottom";
                d.at = "left center";
            }
                break;

            default:
            {
                return false;
            }
                break;
        }
        this.popover.css({
            display: this.options.placement === "inline" ? "" : "block"
        });
        if (d !== false) {
            this.popover.pos(d).css("maxWidth", a(window).width() - this.container.offset().left - 5);
        } else {
            this.popover.css({
                top: "auto",
                right: "auto",
                bottom: "auto",
                left: "auto",
                maxWidth: "none"
            });
        }
        this.popover.addClass(this.options.placement);
        return true;
    },
    _updateComponents: function() {
        this.iconpicker.find(".iconpicker-item.iconpicker-selected").removeClass("iconpicker-selected " + this.options.selectedCustomClass);
        if (this.iconpickerValue) {
            this.iconpicker.find("." + this.options.fullClassFormatter(this.iconpickerValue).replace(/ /g, ".")).parent().addClass("iconpicker-selected " + this.options.selectedCustomClass);
        }
        if (this.hasComponent()) {
            var a = this.component.find("i");
            if (a.length > 0) {
                a.attr("class", this.options.fullClassFormatter(this.iconpickerValue));
            } else {
                this.component.html(this.getHtml());
            }
        }
    },
    _updateFormGroupStatus: function(a) {
        if (this.hasInput()) {
            if (a !== false) {
                this.input.parents(".form-group:first").removeClass("has-error");
            } else {
                this.input.parents(".form-group:first").addClass("has-error");
            }
            return true;
        }
        return false;
    },
    getValid: function(c) {
        if (!b.isString(c)) {
            c = "";
        }
        var d = c === "";
        c = a.trim(c);
        if (b.inArray(c, this.options.icons) || d) {
            return c;
        }
        return false;
    },
    setValue: function(a) {
        var b = this.getValid(a);
        if (b !== false) {
            this.iconpickerValue = b;
            this._trigger("iconpickerSetValue", {
                iconpickerValue: b
            });
            return this.iconpickerValue;
        } else {
            this._trigger("iconpickerInvalid", {
                iconpickerValue: a
            });
            return false;
        }
    },
    getHtml: function() {
        return '<i class="' + this.options.fullClassFormatter(this.iconpickerValue) + '"></i>';
    },
    setSourceValue: function(a) {
        a = this.setValue(a);
        if (a !== false && a !== "") {
            if (this.hasInput()) {
                this.input.val(this.iconpickerValue);
            } else {
                this.element.data("iconpickerValue", this.iconpickerValue);
            }
            this._trigger("iconpickerSetSourceValue", {
                iconpickerValue: a
            });
        }
        return a;
    },
    getSourceValue: function(a) {
        a = a || this.options.defaultValue;
        var b = a;
        if (this.hasInput()) {
            b = this.input.val();
        } else {
            b = this.element.data("iconpickerValue");
        }
        if (b === undefined || b === "" || b === null || b === false) {
            b = a;
        }
        return b;
    },
    hasInput: function() {
        return this.input !== false;
    },
    isInputSearch: function() {
        return this.hasInput() && this.options.inputSearch === true;
    },
    isInputGroup: function() {
        return this.container.is(".input-group");
    },
    isDropdownMenu: function() {
        return this.container.is(".dropdown-menu");
    },
    hasSeparatedSearchInput: function() {
        return this.options.templates.search !== false && !this.isInputSearch();
    },
    hasComponent: function() {
        return this.component !== false;
    },
    hasContainer: function() {
        return this.container !== false;
    },
    getAcceptButton: function() {
        return this.popover.find(".iconpicker-btn-accept");
    },
    getCancelButton: function() {
        return this.popover.find(".iconpicker-btn-cancel");
    },
    getSearchInput: function() {
        return this.popover.find(".iconpicker-search");
    },
    filter: function(c) {
        if (b.isEmpty(c)) {
            this.iconpicker.find(".iconpicker-item").show();
            return a(false);
        } else {
            var d = [];
            this.iconpicker.find(".iconpicker-item").each(function() {
                var b = a(this);
                var e = b.attr("title").toLowerCase();
                var f = false;
                try {
                    f = new RegExp(c, "g");
                } catch (a) {
                    f = false;
                }
                if (f !== false && e.match(f)) {
                    d.push(b);
                    b.show();
                } else {
                    b.hide();
                }
            });
            return d;
        }
    },
    show: function() {
        if (this.popover.hasClass("in")) {
            return false;
        }
        a.iconpicker.batch(a(".iconpicker-popover.in:not(.inline)").not(this.popover), "hide");
        this._trigger("iconpickerShow");
        this.updatePlacement();
        this.popover.addClass("in");
        setTimeout(a.proxy(function() {
            this.popover.css("display", this.isInline() ? "" : "block");
            this._trigger("iconpickerShown");
        }, this), this.options.animation ? 300 : 1);
    },
    hide: function() {
        if (!this.popover.hasClass("in")) {
            return false;
        }
        this._trigger("iconpickerHide");
        this.popover.removeClass("in");
        setTimeout(a.proxy(function() {
            this.popover.css("display", "none");
            this.getSearchInput().val("");
            this.filter("");
            this._trigger("iconpickerHidden");
        }, this), this.options.animation ? 300 : 1);
    },
    toggle: function() {
        if (this.popover.is(":visible")) {
            this.hide();
        } else {
            this.show(true);
        }
    },
    update: function(a, b) {
        a = a ? a : this.getSourceValue(this.iconpickerValue);
        this._trigger("iconpickerUpdate");
        if (b === true) {
            a = this.setValue(a);
        } else {
            a = this.setSourceValue(a);
            this._updateFormGroupStatus(a !== false);
        }
        if (a !== false) {
            this._updateComponents();
        }
        this._trigger("iconpickerUpdated");
        return a;
    },
    destroy: function() {
        this._trigger("iconpickerDestroy");
        this.element.removeData("iconpicker").removeData("iconpickerValue").removeClass("iconpicker-element");
        this._unbindElementEvents();
        this._unbindWindowEvents();
        a(this.popover).remove();
        this._trigger("iconpickerDestroyed");
    },
    disable: function() {
        if (this.hasInput()) {
            this.input.prop("disabled", true);
            return true;
        }
        return false;
    },
    enable: function() {
        if (this.hasInput()) {
            this.input.prop("disabled", false);
            return true;
        }
        return false;
    },
    isDisabled: function() {
        if (this.hasInput()) {
            return this.input.prop("disabled") === true;
        }
        return false;
    },
    isInline: function() {
        return this.options.placement === "inline" || this.popover.hasClass("inline");
    }
};