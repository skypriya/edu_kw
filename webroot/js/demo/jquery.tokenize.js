! function(e, t) {
    var n = 8,
        o = 9,
        s = 13,
        i = 27,
        a = 38,
        r = 40,
        d = null,
        h = "tokenize",
        l = function(t, n) {
            if (!n.data(h)) {
                var o = new e.tokenize(e.extend({}, e.fn.tokenize.defaults, t));
                n.data(h, o), o.init(n)
            }
            return n.data(h)
        };
    e.tokenize = function(t) {
        null == t && (t = e.fn.tokenize.defaults), this.options = t
    }, e.extend(e.tokenize.prototype, {
        init: function(t) {
            var n = this;
            this.select = t.attr("multiple", "multiple").css({
                margin: 0,
                padding: 0,
                border: 0
            }).hide(), this.container = e("<div />").attr("class", this.select.attr("class")).addClass("Tokenize"), 1 == this.options.maxElements && this.container.addClass("OnlyOne"), this.dropdown = e("<ul />").addClass("Dropdown"), this.tokensContainer = e("<ul />").addClass("TokensContainer"), this.options.autosize && this.tokensContainer.addClass("Autosize"), this.searchToken = e("<li />").addClass("TokenSearch").appendTo(this.tokensContainer), this.searchInput = e("<input />").appendTo(this.searchToken), this.options.searchMaxLength > 0 && this.searchInput.attr("maxlength", this.options.searchMaxLength), this.select.prop("disabled") && this.disable(), this.options.sortable && (void 0 !== e.ui ? this.tokensContainer.sortable({
                items: "li.Token",
                cursor: "move",
                placeholder: "Token MovingShadow",
                forcePlaceholderSize: !0,
                update: function() {
                    n.updateOrder()
                },
                start: function() {
                    n.searchToken.hide()
                },
                stop: function() {
                    n.searchToken.show()
                }
            }).disableSelection() : (this.options.sortable = !1, console.error("jQuery UI is not loaded, sortable option has been disabled"))), this.container.append(this.tokensContainer).append(this.dropdown).insertAfter(this.select), this.tokensContainer.on("click", function(e) {
                e.stopImmediatePropagation(), n.searchInput.get(0).focus(), n.updatePlaceholder(), n.dropdown.is(":hidden") && "" != n.searchInput.val() && n.search()
            }), this.searchInput.on("blur", function() {
                n.tokensContainer.removeClass("Focused")
            }), this.searchInput.on("focus click", function() {
                n.tokensContainer.addClass("Focused"), n.options.displayDropdownOnFocus && "select" == n.options.datas && n.search()
            }), this.searchInput.on("keydown", function(e) {
                n.resizeSearchInput(), n.keydown(e)
            }), this.searchInput.on("keyup", function(e) {
                n.keyup(e)
            }), this.searchInput.on("keypress", function(e) {
                n.keypress(e)
            }), this.searchInput.on("paste", function() {
                setTimeout(function() {
                    n.resizeSearchInput()
                }, 10), setTimeout(function() {
                    var t;
                    (t = Array.isArray(n.options.delimiter) ? n.searchInput.val().split(new RegExp(n.options.delimiter.join("|"), "g")) : n.searchInput.val().split(n.options.delimiter)).length > 1 && e.each(t, function(e, t) {
                        n.tokenAdd(t.trim(), "")
                    })
                }, 20)
            }), e(document).on("click", function() {
                n.dropdownHide(), n.searchInput.val() && n.tokenAdd(n.searchInput.val(), "")
            }), e("#tag_submit").on("click", function() {
                n.dropdownHide(), n.searchInput.val() && n.tokenAdd(n.searchInput.val(), "")
            }), this.resizeSearchInput(), this.remap(!0), this.updatePlaceholder()
        },
        updateOrder: function() {
            if (this.options.sortable) {
                var t, n, o = this;
                e.each(this.tokensContainer.sortable("toArray", {
                    attribute: "data-value"
                }), function(s, i) {
                    n = e('option[value="' + i + '"]', o.select), null == t ? n.prependTo(o.select) : t.after(n), t = n
                }), this.options.onReorder(this)
            }
        },
        updatePlaceholder: function() {
            this.options.placeholder && (null == this.placeholder && (this.placeholder = e("<li />").addClass("Placeholder").html(this.options.placeholder), this.placeholder.insertBefore(e("li:first-child", this.tokensContainer))), 0 == this.searchInput.val().length && 0 == e("li.Token", this.tokensContainer).length ? this.placeholder.show() : this.placeholder.hide())
        },
        dropdownShow: function() {
            this.dropdown.show(), this.options.onDropdownShow(this)
        },
        dropdownPrev: function() {
            e("li.Hover", this.dropdown).length > 0 ? e("li.Hover", this.dropdown).is("li:first-child") ? (e("li.Hover", this.dropdown).removeClass("Hover"), e("li:last-child", this.dropdown).addClass("Hover")) : e("li.Hover", this.dropdown).removeClass("Hover").prev().addClass("Hover") : e("li:first", this.dropdown).addClass("Hover")
        },
        dropdownNext: function() {
            e("li.Hover", this.dropdown).length > 0 ? e("li.Hover", this.dropdown).is("li:last-child") ? (e("li.Hover", this.dropdown).removeClass("Hover"), e("li:first-child", this.dropdown).addClass("Hover")) : e("li.Hover", this.dropdown).removeClass("Hover").next().addClass("Hover") : e("li:first", this.dropdown).addClass("Hover")
        },
        dropdownAddItem: function(t, n, o) {
            if (o = o || n, !e('li[data-value="' + t + '"]', this.tokensContainer).length) {
                var s = this,
                    i = e("<li />").attr("data-value", t).attr("data-text", n).html(o).on("click", function(t) {
                        t.stopImmediatePropagation(), s.tokenAdd(e(this).attr("data-value"), e(this).attr("data-text"))
                    }).on("mouseover", function() {
                        e(this).addClass("Hover")
                    }).on("mouseout", function() {
                        e("li", s.dropdown).removeClass("Hover")
                    });
                this.dropdown.append(i), this.options.onDropdownAddItem(t, n, o, this)
            }
            return this
        },
        dropdownHide: function() {
            this.dropdownReset(), this.dropdown.hide()
        },
        dropdownReset: function() {
            this.dropdown.html("")
        },
        resizeSearchInput: function() {
            this.searchInput.attr("size", Number(this.searchInput.val().length) + 5), this.updatePlaceholder()
        },
        resetSearchInput: function() {
            this.searchInput.val(""), this.resizeSearchInput()
        },
        resetPendingTokens: function() {
            e("li.PendingDelete", this.tokensContainer).removeClass("PendingDelete")
        },
        keypress: function(e) {
            var t = !1;
            Array.isArray(this.options.delimiter) ? this.options.delimiter.indexOf(String.fromCharCode(e.which)) >= 0 && (t = !0) : String.fromCharCode(e.which) == this.options.delimiter && (t = !0), t && (e.preventDefault(), this.tokenAdd(this.searchInput.val(), ""))
        },
        keydown: function(t) {
            switch (t.keyCode) {
                case n:
                    0 == this.searchInput.val().length && (t.preventDefault(), e("li.Token.PendingDelete", this.tokensContainer).length ? this.tokenRemove(e("li.Token.PendingDelete").attr("data-value")) : e("li.Token:last", this.tokensContainer).addClass("PendingDelete"), this.dropdownHide());
                    break;
                case o:
                case s:
                    if (e("li.Hover", this.dropdown).length) {
                        var d = e("li.Hover", this.dropdown);
                        t.preventDefault(), this.tokenAdd(d.attr("data-value"), d.attr("data-text"))
                    } else this.searchInput.val() && (t.preventDefault(), this.tokenAdd(this.searchInput.val(), ""));
                    this.resetPendingTokens();
                    break;
                case i:
                    this.resetSearchInput(), this.dropdownHide(), this.resetPendingTokens();
                    break;
                case a:
                    t.preventDefault(), this.dropdownPrev();
                    break;
                case r:
                    t.preventDefault(), this.dropdownNext();
                    break;
                default:
                    this.resetPendingTokens()
            }
        },
        keyup: function(e) {
            switch (this.updatePlaceholder(), e.keyCode) {
                case o:
                case s:
                case i:
                case a:
                case r:
                    break;
                case n:
                    this.searchInput.val() ? this.search() : this.dropdownHide();
                    break;
                default:
                    this.searchInput.val() && this.search()
            }
        },
        search: function() {
            var t = this,
                n = 1;
            if (this.options.maxElements > 0 && e("li.Token", this.tokensContainer).length >= this.options.maxElements || this.searchInput.val().length < this.options.searchMinLength) return !1;
            if ("select" == this.options.datas) {
                var o = !1,
                    s = new RegExp(this.searchInput.val().replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&"), "i");
                this.dropdownReset(), e("option", this.select).not(":selected, :disabled").each(function() {
                    if (!(n <= t.options.nbDropdownElements)) return !1;
                    s.test(e(this).html()) && (t.dropdownAddItem(e(this).attr("value"), e(this).html()), o = !0, n++)
                }), o ? (e("li:first", this.dropdown).addClass("Hover"), this.dropdownShow()) : this.dropdownHide()
            } else this.debounce(function() {
                void 0 !== this.ajax && this.ajax.abort(), this.ajax = e.ajax({
                    url: t.options.datas,
                    data: t.options.searchParam + "=" + encodeURIComponent(t.searchInput.val()),
                    dataType: t.options.dataType,
                    success: function(o) {
                        if (o && (t.dropdownReset(), e.each(o, function(e, o) {
                                if (!(n <= t.options.nbDropdownElements)) return !1;
                                var s;
                                o[t.options.htmlField] && (s = o[t.options.htmlField]), t.dropdownAddItem(o[t.options.valueField], o[t.options.textField], s), n++
                            }), e("li", t.dropdown).length)) return e("li:first", t.dropdown).addClass("Hover"), t.dropdownShow(), !0;
                        t.dropdownHide()
                    },
                    error: function(e, n) {
                        t.options.onAjaxError(t, e, n)
                    }
                })
            }, this.options.debounce)
        },
        debounce: function(e, t) {
            var n = this,
                o = arguments;
            d && clearTimeout(d), d = setTimeout(function() {
                e.apply(n, o), d = null
            }, t || this.options.debounce)
        },
        tokenAdd: function(t, n, o) {
            var s = 0;
            if ($("#global-tokenize").each(function() {
                    $("option", this).each(function() {
                        $(this).text().toLowerCase() == t.toLowerCase().trim() && (s = 1)
                    })
                }), null == (t = this.escape(t).trim()) || "" == t) return this;
            if (1 == s) return this.resetSearchInput(), this;
            if (n = n || t, o = o || !1, this.options.maxElements > 0 && e("li.Token", this.tokensContainer).length >= this.options.maxElements) return this.resetSearchInput(), this;
            var i = this,
                a = e("<a />").addClass("Close").html("&#215;").on("click", function(e) {
                    e.stopImmediatePropagation(), i.tokenRemove(t)
                });
            if (e('option[value="' + t + '"]', this.select).length) o || !0 !== e('option[value="' + t + '"]', this.select).attr("selected") && !0 !== e('option[value="' + t + '"]', this.select).prop("selected") || this.options.onDuplicateToken(t, n, this), e('option[value="' + t + '"]', this.select).attr("selected", !0).prop("selected", !0);
            else {
                if (!(this.options.newElements || !this.options.newElements && e('li[data-value="' + t + '"]', this.dropdown).length > 0)) return this.resetSearchInput(), this;
                var r = e("<option />").attr("selected", !0).attr("value", t).attr("data-type", "custom").prop("selected", !0).html(n);
                this.select.append(r)
            }
            return e('li.Token[data-value="' + t + '"]', this.tokensContainer).length > 0 ? this : (e("<li />").addClass("Token").attr("data-value", t).append("<span>" + n + "</span>").prepend(a).insertBefore(this.searchToken), o || this.options.onAddToken(t, n, this), this.resetSearchInput(), this.dropdownHide(), this.updateOrder(), this)
        },
        tokenRemove: function(t) {
            var n = e('option[value="' + t + '"]', this.select);
            return "custom" == n.attr("data-type") ? n.remove() : n.removeAttr("selected").prop("selected", !1), e('li.Token[data-value="' + t + '"]', this.tokensContainer).remove(), this.options.onRemoveToken(t, this), this.resizeSearchInput(), this.dropdownHide(), this.updateOrder(), this
        },
        clear: function() {
            var t = this;
            return e("li.Token", this.tokensContainer).each(function() {
                t.tokenRemove(e(this).attr("data-value"))
            }), this.options.onClear(this), this.dropdownHide(), this
        },
        disable: function() {
            return this.select.prop("disabled", !0), this.searchInput.prop("disabled", !0), this.container.addClass("Disabled"), this.options.sortable && this.tokensContainer.sortable("disable"), this
        },
        enable: function() {
            return this.select.prop("disabled", !1), this.searchInput.prop("disabled", !1), this.container.removeClass("Disabled"), this.options.sortable && this.tokensContainer.sortable("enable"), this
        },
        remap: function(t) {
            var n = this,
                o = e("option:selected", this.select);
            return t = t || !1, this.clear(), o.each(function() {
                n.tokenAdd(e(this).val(), e(this).html(), t)
            }), this
        },
        toArray: function() {
            var t = [];
            return e("option:selected", this.select).each(function() {
                t.push(e(this).val())
            }), t
        },
        escape: function(e) {
            var t = document.createElement("div");
            return t.innerHTML = e, e = t.textContent || t.innerText || "", String(e).replace(/["]/g, function() {
                return ""
            })
        }
    }), e.fn.tokenize = function(t) {
        t = t || {};
        var n = this.filter("select");
        if (n.length > 1) {
            var o = [];
            return n.each(function() {
                o.push(l(t, e(this)))
            }), o
        }
        return l(t, e(this))
    }, e.fn.tokenize.defaults = {
        datas: "select",
        placeholder: !1,
        searchParam: "search",
        searchMaxLength: 0,
        searchMinLength: 0,
        debounce: 0,
        delimiter: ",",
        newElements: !0,
        autosize: !1,
        nbDropdownElements: 10,
        displayDropdownOnFocus: !1,
        maxElements: 0,
        sortable: !1,
        dataType: "json",
        valueField: "value",
        textField: "text",
        htmlField: "html",
        onAddToken: function(e, t, n) {},
        onRemoveToken: function(e, t) {},
        onClear: function(e) {},
        onReorder: function(e) {},
        onDropdownAddItem: function(e, t, n, o) {},
        onDropdownShow: function(e) {},
        onDuplicateToken: function(e, t, n) {},
        onAjaxError: function(e, t, n) {}
    }
}(jQuery);