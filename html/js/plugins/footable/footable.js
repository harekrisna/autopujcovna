/*!
 * FooTable - Awesome Responsive Tables
 * Version : 2.0.3
 * http://fooplugins.com/plugins/footable-jquery/
 *
 * Requires jQuery - http://jquery.com/
 *
 * Copyright 2014 Steven Usher & Brad Vincent
 * Released under the MIT license
 * You are free to use FooTable in commercial projects as long as this copyright header is left intact.
 *
 * Date: 11 Nov 2014
 */
(function(e, t) {
    function a() {
        var e = this;
        e.id = null, e.busy = !1, e.start = function(t, a) {
            e.busy || (e.stop(), e.id = setTimeout(function() {
                t(), e.id = null, e.busy = !1
            }, a), e.busy = !0)
        }, e.stop = function() {
            null !== e.id && (clearTimeout(e.id), e.id = null, e.busy = !1)
        }
    }

    function i(i, o, n) {
        var r = this;
        r.id = n, r.table = i, r.options = o, r.breakpoints = [], r.breakpointNames = "", r.columns = {}, r.plugins = t.footable.plugins.load(r);
        var l = r.options,
            d = l.classes,
            s = l.events,
            u = l.triggers,
            f = 0;
        return r.timers = {
            resize: new a,
            register: function(e) {
                return r.timers[e] = new a, r.timers[e]
            }
        }, r.init = function() {
            var a = e(t),
                i = e(r.table);
            if (t.footable.plugins.init(r), i.hasClass(d.loaded)) return r.raise(s.alreadyInitialized), undefined;
            r.raise(s.initializing), i.addClass(d.loading), i.find(l.columnDataSelector).each(function() {
                var e = r.getColumnData(this);
                r.columns[e.index] = e
            });
            for (var o in l.breakpoints) r.breakpoints.push({
                name: o,
                width: l.breakpoints[o]
            }), r.breakpointNames += o + " ";
            r.breakpoints.sort(function(e, t) {
                return e.width - t.width
            }), i.unbind(u.initialize).bind(u.initialize, function() {
                i.removeData("footable_info"), i.data("breakpoint", ""), i.trigger(u.resize), i.removeClass(d.loading), i.addClass(d.loaded).addClass(d.main), r.raise(s.initialized)
            }).unbind(u.redraw).bind(u.redraw, function() {
                r.redraw()
            }).unbind(u.resize).bind(u.resize, function() {
                r.resize()
            }).unbind(u.expandFirstRow).bind(u.expandFirstRow, function() {
                i.find(l.toggleSelector).first().not("." + d.detailShow).trigger(u.toggleRow)
            }).unbind(u.expandAll).bind(u.expandAll, function() {
                i.find(l.toggleSelector).not("." + d.detailShow).trigger(u.toggleRow)
            }).unbind(u.collapseAll).bind(u.collapseAll, function() {
                i.find("." + d.detailShow).trigger(u.toggleRow)
            }), i.trigger(u.initialize), a.bind("resize.footable", function() {
                r.timers.resize.stop(), r.timers.resize.start(function() {
                    r.raise(u.resize)
                }, l.delay)
            })
        }, r.addRowToggle = function() {
            if (l.addRowToggle) {
                var t = e(r.table),
                    a = !1;
                t.find("span." + d.toggle).remove();
                for (var i in r.columns) {
                    var o = r.columns[i];
                    if (o.toggle) {
                        a = !0;
                        var n = "> tbody > tr:not(." + d.detail + ",." + d.disabled + ") > td:nth-child(" + (parseInt(o.index, 10) + 1) + ")," + "> tbody > tr:not(." + d.detail + ",." + d.disabled + ") > th:nth-child(" + (parseInt(o.index, 10) + 1) + ")";
                        return t.find(n).not("." + d.detailCell).prepend(e(l.toggleHTMLElement).addClass(d.toggle)), undefined
                    }
                }
                a || t.find("> tbody > tr:not(." + d.detail + ",." + d.disabled + ") > td:first-child").add("> tbody > tr:not(." + d.detail + ",." + d.disabled + ") > th:first-child").not("." + d.detailCell).prepend(e(l.toggleHTMLElement).addClass(d.toggle))
            }
        }, r.setColumnClasses = function() {
            var t = e(r.table);
            for (var a in r.columns) {
                var i = r.columns[a];
                if (null !== i.className) {
                    var o = "",
                        n = !0;
                    e.each(i.matches, function(e, t) {
                        n || (o += ", "), o += "> tbody > tr:not(." + d.detail + ") > td:nth-child(" + (parseInt(t, 10) + 1) + ")", n = !1
                    }), t.find(o).not("." + d.detailCell).addClass(i.className)
                }
            }
        }, r.bindToggleSelectors = function() {
            var t = e(r.table);
            r.hasAnyBreakpointColumn() && (t.find(l.toggleSelector).unbind(u.toggleRow).bind(u.toggleRow, function() {
                var t = e(this).is("tr") ? e(this) : e(this).parents("tr:first");
                r.toggleDetail(t)
            }), t.find(l.toggleSelector).unbind("click.footable").bind("click.footable", function(a) {
                t.is(".breakpoint") && e(a.target).is("td,th,." + d.toggle) && e(this).trigger(u.toggleRow)
            }))
        }, r.parse = function(e, t) {
            var a = l.parsers[t.type] || l.parsers.alpha;
            return a(e)
        }, r.getColumnData = function(t) {
            var a = e(t),
                i = a.data("hide"),
                o = a.index();
            i = i || "", i = jQuery.map(i.split(","), function(e) {
                return jQuery.trim(e)
            });
            var n = {
                index: o,
                hide: {},
                type: a.data("type") || "alpha",
                name: a.data("name") || e.trim(a.text()),
                ignore: a.data("ignore") || !1,
                toggle: a.data("toggle") || !1,
                className: a.data("class") || null,
                matches: [],
                names: {},
                group: a.data("group") || null,
                groupName: null,
                isEditable: a.data("editable")
            };
            if (null !== n.group) {
                var d = e(r.table).find('> thead > tr.footable-group-row > th[data-group="' + n.group + '"], > thead > tr.footable-group-row > td[data-group="' + n.group + '"]').first();
                n.groupName = r.parse(d, {
                    type: "alpha"
                })
            }
            var u = parseInt(a.prev().attr("colspan") || 0, 10);
            f += u > 1 ? u - 1 : 0;
            var p = parseInt(a.attr("colspan") || 0, 10),
                c = n.index + f;
            if (p > 1) {
                var b = a.data("names");
                b = b || "", b = b.split(",");
                for (var g = 0; p > g; g++) n.matches.push(g + c), b.length > g && (n.names[g + c] = b[g])
            } else n.matches.push(c);
            n.hide["default"] = "all" === a.data("hide") || e.inArray("default", i) >= 0;
            var h = !1;
            for (var m in l.breakpoints) n.hide[m] = "all" === a.data("hide") || e.inArray(m, i) >= 0, h = h || n.hide[m];
            n.hasBreakpoint = h;
            var v = r.raise(s.columnData, {
                column: {
                    data: n,
                    th: t
                }
            });
            return v.column.data
        }, r.getViewportWidth = function() {
            return window.innerWidth || (document.body ? document.body.offsetWidth : 0)
        }, r.calculateWidth = function(e, t) {
            return jQuery.isFunction(l.calculateWidthOverride) ? l.calculateWidthOverride(e, t) : (t.viewportWidth < t.width && (t.width = t.viewportWidth), t.parentWidth < t.width && (t.width = t.parentWidth), t)
        }, r.hasBreakpointColumn = function(e) {
            for (var t in r.columns)
                if (r.columns[t].hide[e]) {
                    if (r.columns[t].ignore) continue;
                    return !0
                }
            return !1
        }, r.hasAnyBreakpointColumn = function() {
            for (var e in r.columns)
                if (r.columns[e].hasBreakpoint) return !0;
            return !1
        }, r.resize = function() {
            var t = e(r.table);
            if (t.is(":visible")) {
                if (!r.hasAnyBreakpointColumn()) return t.trigger(u.redraw), undefined;
                var a = {
                    width: t.width(),
                    viewportWidth: r.getViewportWidth(),
                    parentWidth: t.parent().width()
                };
                a = r.calculateWidth(t, a);
                var i = t.data("footable_info");
                if (t.data("footable_info", a), r.raise(s.resizing, {
                        old: i,
                        info: a
                    }), !i || i && i.width && i.width !== a.width) {
                    for (var o, n = null, l = 0; r.breakpoints.length > l; l++)
                        if (o = r.breakpoints[l], o && o.width && a.width <= o.width) {
                            n = o;
                            break
                        }
                    var d = null === n ? "default" : n.name,
                        f = r.hasBreakpointColumn(d),
                        p = t.data("breakpoint");
                    t.data("breakpoint", d).removeClass("default breakpoint").removeClass(r.breakpointNames).addClass(d + (f ? " breakpoint" : "")), d !== p && (t.trigger(u.redraw), r.raise(s.breakpoint, {
                        breakpoint: d,
                        info: a
                    }))
                }
                r.raise(s.resized, {
                    old: i,
                    info: a
                })
            }
        }, r.redraw = function() {
            r.addRowToggle(), r.bindToggleSelectors(), r.setColumnClasses();
            var t = e(r.table),
                a = t.data("breakpoint"),
                i = r.hasBreakpointColumn(a);
            t.find("> tbody > tr:not(." + d.detail + ")").data("detail_created", !1).end().find("> thead > tr:last-child > th").each(function() {
                var i = r.columns[e(this).index()],
                    o = "",
                    n = !0;
                e.each(i.matches, function(e, t) {
                    n || (o += ", ");
                    var a = t + 1;
                    o += "> tbody > tr:not(." + d.detail + ") > td:nth-child(" + a + ")", o += ", > tfoot > tr:not(." + d.detail + ") > td:nth-child(" + a + ")", o += ", > colgroup > col:nth-child(" + a + ")", n = !1
                }), o += ', > thead > tr[data-group-row="true"] > th[data-group="' + i.group + '"]';
                var l = t.find(o).add(this);
                if ("" !== a && (i.hide[a] === !1 ? l.addClass("footable-visible").show() : l.removeClass("footable-visible").hide()), 1 === t.find("> thead > tr.footable-group-row").length) {
                    var s = t.find('> thead > tr:last-child > th[data-group="' + i.group + '"]:visible, > thead > tr:last-child > th[data-group="' + i.group + '"]:visible'),
                        u = t.find('> thead > tr.footable-group-row > th[data-group="' + i.group + '"], > thead > tr.footable-group-row > td[data-group="' + i.group + '"]'),
                        f = 0;
                    e.each(s, function() {
                        f += parseInt(e(this).attr("colspan") || 1, 10)
                    }), f > 0 ? u.attr("colspan", f).show() : u.hide()
                }
            }).end().find("> tbody > tr." + d.detailShow).each(function() {
                r.createOrUpdateDetailRow(this)
            }), t.find("[data-bind-name]").each(function() {
                r.toggleInput(this)
            }), t.find("> tbody > tr." + d.detailShow + ":visible").each(function() {
                var t = e(this).next();
                t.hasClass(d.detail) && (i ? t.show() : t.hide())
            }), t.find("> thead > tr > th.footable-last-column, > tbody > tr > td.footable-last-column").removeClass("footable-last-column"), t.find("> thead > tr > th.footable-first-column, > tbody > tr > td.footable-first-column").removeClass("footable-first-column"), t.find("> thead > tr, > tbody > tr").find("> th.footable-visible:last, > td.footable-visible:last").addClass("footable-last-column").end().find("> th.footable-visible:first, > td.footable-visible:first").addClass("footable-first-column"), r.raise(s.redrawn)
        }, r.toggleDetail = function(t) {
            var a = t.jquery ? t : e(t),
                i = a.next();
            a.hasClass(d.detailShow) ? (a.removeClass(d.detailShow), i.hasClass(d.detail) && i.hide(), r.raise(s.rowCollapsed, {
                row: a[0]
            })) : (r.createOrUpdateDetailRow(a[0]), a.addClass(d.detailShow).next().show(), r.raise(s.rowExpanded, {
                row: a[0]
            }))
        }, r.removeRow = function(t) {
            var a = t.jquery ? t : e(t);
            a.hasClass(d.detail) && (a = a.prev());
            var i = a.next();
            a.data("detail_created") === !0 && i.remove(), a.remove(), r.raise(s.rowRemoved)
        }, r.appendRow = function(t) {
            var a = t.jquery ? t : e(t);
            e(r.table).find("tbody").append(a), r.redraw()
        }, r.getColumnFromTdIndex = function(t) {
            var a = null;
            for (var i in r.columns)
                if (e.inArray(t, r.columns[i].matches) >= 0) {
                    a = r.columns[i];
                    break
                }
            return a
        }, r.createOrUpdateDetailRow = function(t) {
            var a, i = e(t),
                o = i.next(),
                n = [];
            if (i.data("detail_created") === !0) return !0;
            if (i.is(":hidden")) return !1;
            if (r.raise(s.rowDetailUpdating, {
                    row: i,
                    detail: o
                }), i.find("> td:hidden").each(function() {
                    var t = e(this).index(),
                        a = r.getColumnFromTdIndex(t),
                        i = a.name;
                    if (a.ignore === !0) return !0;
                    t in a.names && (i = a.names[t]);
                    var o = e(this).attr("data-bind-name");
                    if (null != o && e(this).is(":empty")) {
                        var l = e("." + d.detailInnerValue + "[" + 'data-bind-value="' + o + '"]');
                        e(this).html(e(l).contents().detach())
                    }
                    var s;
                    return a.isEditable !== !1 && (a.isEditable || e(this).find(":input").length > 0) && (null == o && (o = "bind-" + e.now() + "-" + t, e(this).attr("data-bind-name", o)), s = e(this).contents().detach()), s || (s = e(this).contents().clone(!0, !0)), n.push({
                        name: i,
                        value: r.parse(this, a),
                        display: s,
                        group: a.group,
                        groupName: a.groupName,
                        bindName: o
                    }), !0
                }), 0 === n.length) return !1;
            var u = i.find("> td:visible").length,
                f = o.hasClass(d.detail);
            return f || (o = e('<tr class="' + d.detail + '"><td class="' + d.detailCell + '"><div class="' + d.detailInner + '"></div></td></tr>'), i.after(o)), o.find("> td:first").attr("colspan", u), a = o.find("." + d.detailInner).empty(), l.createDetail(a, n, l.createGroupedDetail, l.detailSeparator, d), i.data("detail_created", !0), r.raise(s.rowDetailUpdated, {
                row: i,
                detail: o
            }), !f
        }, r.raise = function(t, a) {
            r.options.debug === !0 && e.isFunction(r.options.log) && r.options.log(t, "event"), a = a || {};
            var i = {
                ft: r
            };
            e.extend(!0, i, a);
            var o = e.Event(t, i);
            return o.ft || e.extend(!0, o, i), e(r.table).trigger(o), o
        }, r.reset = function() {
            var t = e(r.table);
            t.removeData("footable_info").data("breakpoint", "").removeClass(d.loading).removeClass(d.loaded), t.find(l.toggleSelector).unbind(u.toggleRow).unbind("click.footable"), t.find("> tbody > tr").removeClass(d.detailShow), t.find("> tbody > tr." + d.detail).remove(), r.raise(s.reset)
        }, r.toggleInput = function(t) {
            var a = e(t).attr("data-bind-name");
            if (null != a) {
                var i = e("." + d.detailInnerValue + "[" + 'data-bind-value="' + a + '"]');
                null != i && (e(t).is(":visible") ? e(i).is(":empty") || e(t).html(e(i).contents().detach()) : e(t).is(":empty") || e(i).html(e(t).contents().detach()))
            }
        }, r.init(), r
    }
    t.footable = {
        options: {
            delay: 100,
            breakpoints: {
                phone: 480,
                tablet: 1024
            },
            parsers: {
                alpha: function(t) {
                    return e(t).data("value") || e.trim(e(t).text())
                },
                numeric: function(t) {
                    var a = e(t).data("value") || e(t).text().replace(/[^0-9.\-]/g, "");
                    return a = parseFloat(a), isNaN(a) && (a = 0), a
                }
            },
            addRowToggle: !0,
            calculateWidthOverride: null,
            toggleSelector: " > tbody > tr:not(.footable-row-detail)",
            columnDataSelector: "> thead > tr:last-child > th, > thead > tr:last-child > td",
            detailSeparator: ":",
            toggleHTMLElement: "<span />",
            createGroupedDetail: function(e) {
                for (var t = {
                        _none: {
                            name: null,
                            data: []
                        }
                    }, a = 0; e.length > a; a++) {
                    var i = e[a].group;
                    null !== i ? (i in t || (t[i] = {
                        name: e[a].groupName || e[a].group,
                        data: []
                    }), t[i].data.push(e[a])) : t._none.data.push(e[a])
                }
                return t
            },
            createDetail: function(t, a, i, o, n) {
                var r = i(a);
                for (var l in r)
                    if (0 !== r[l].data.length) {
                        "_none" !== l && t.append('<div class="' + n.detailInnerGroup + '">' + r[l].name + "</div>");
                        for (var d = 0; r[l].data.length > d; d++) {
                            var s = r[l].data[d].name ? o : "";
                            t.append(e("<div></div>").addClass(n.detailInnerRow).append(e("<div></div>").addClass(n.detailInnerName).append(r[l].data[d].name + s)).append(e("<div></div>").addClass(n.detailInnerValue).attr("data-bind-value", r[l].data[d].bindName).append(r[l].data[d].display)))
                        }
                    }
            },
            classes: {
                main: "footable",
                loading: "footable-loading",
                loaded: "footable-loaded",
                toggle: "footable-toggle",
                disabled: "footable-disabled",
                detail: "footable-row-detail",
                detailCell: "footable-row-detail-cell",
                detailInner: "footable-row-detail-inner",
                detailInnerRow: "footable-row-detail-row",
                detailInnerGroup: "footable-row-detail-group",
                detailInnerName: "footable-row-detail-name",
                detailInnerValue: "footable-row-detail-value",
                detailShow: "footable-detail-show"
            },
            triggers: {
                initialize: "footable_initialize",
                resize: "footable_resize",
                redraw: "footable_redraw",
                toggleRow: "footable_toggle_row",
                expandFirstRow: "footable_expand_first_row",
                expandAll: "footable_expand_all",
                collapseAll: "footable_collapse_all"
            },
            events: {
                alreadyInitialized: "footable_already_initialized",
                initializing: "footable_initializing",
                initialized: "footable_initialized",
                resizing: "footable_resizing",
                resized: "footable_resized",
                redrawn: "footable_redrawn",
                breakpoint: "footable_breakpoint",
                columnData: "footable_column_data",
                rowDetailUpdating: "footable_row_detail_updating",
                rowDetailUpdated: "footable_row_detail_updated",
                rowCollapsed: "footable_row_collapsed",
                rowExpanded: "footable_row_expanded",
                rowRemoved: "footable_row_removed",
                reset: "footable_reset"
            },
            debug: !1,
            log: null
        },
        version: {
            major: 0,
            minor: 5,
            toString: function() {
                return t.footable.version.major + "." + t.footable.version.minor
            },
            parse: function(e) {
                var t = /(\d+)\.?(\d+)?\.?(\d+)?/.exec(e);
                return {
                    major: parseInt(t[1], 10) || 0,
                    minor: parseInt(t[2], 10) || 0,
                    patch: parseInt(t[3], 10) || 0
                }
            }
        },
        plugins: {
            _validate: function(a) {
                if (!e.isFunction(a)) return t.footable.options.debug === !0 && console.error('Validation failed, expected type "function", received type "{0}".', typeof a), !1;
                var i = new a;
                return "string" != typeof i.name ? (t.footable.options.debug === !0 && console.error('Validation failed, plugin does not implement a string property called "name".', i), !1) : e.isFunction(i.init) ? (t.footable.options.debug === !0 && console.log('Validation succeeded for plugin "' + i.name + '".', i), !0) : (t.footable.options.debug === !0 && console.error('Validation failed, plugin "' + i.name + '" does not implement a function called "init".', i), !1)
            },
            registered: [],
            register: function(a, i) {
                t.footable.plugins._validate(a) && (t.footable.plugins.registered.push(a), "object" == typeof i && e.extend(!0, t.footable.options, i))
            },
            load: function(e) {
                var a, i, o = [];
                for (i = 0; t.footable.plugins.registered.length > i; i++) try {
                    a = t.footable.plugins.registered[i], o.push(new a(e))
                } catch (n) {
                    t.footable.options.debug === !0 && console.error(n)
                }
                return o
            },
            init: function(e) {
                for (var a = 0; e.plugins.length > a; a++) try {
                    e.plugins[a].init(e)
                } catch (i) {
                    t.footable.options.debug === !0 && console.error(i)
                }
            }
        }
    };
    var o = 0;
    e.fn.footable = function(a) {
        a = a || {};
        var n = e.extend(!0, {}, t.footable.options, a);
        return this.each(function() {
            o++;
            var t = new i(this, n, o);
            e(this).data("footable", t)
        })
    }
})(jQuery, window);;
(function(e, t, undefined) {
    function a(t) {
        var a = e("<th>" + t.title + "</th>");
        return e.isPlainObject(t.data) && a.data(t.data), e.isPlainObject(t.style) && a.css(t.style), t.className && a.addClass(t.className), a
    }

    function o(t, o) {
        var i = t.find("thead");
        0 === i.size() && (i = e("<thead>").appendTo(t));
        for (var n = e("<tr>").appendTo(i), r = 0, l = o.cols.length; l > r; r++) n.append(a(o.cols[r]))
    }

    function i(t) {
        var a = t.find("tbody");
        0 === a.size() && (a = e("<tbody>").appendTo(t))
    }

    function n(t, a, o) {
        if (o) {
            t.attr("data-page-size", o["page-size"]);
            var i = t.find("tfoot");
            0 === i.size() && (i = e('<tfoot class="hide-if-no-paging"></tfoot>').appendTo(t)), i.append("<tr><td colspan=" + a.length + "></td></tr>");
            var n = e("<div>").appendTo(i.find("tr:last-child td"));
            n.addClass(o["pagination-class"])
        }
    }

    function r(t) {
        for (var a = t[0], o = 0, i = t.length; i > o; o++) {
            var n = t[o];
            if (n.data && (n.data.toggle === !0 || "true" === n.data.toggle)) return
        }
        a.data = e.extend(a.data, {
            toggle: !0
        })
    }

    function l(e, t, a) {
        0 === e.find("tr.emptyInfo").size() && e.find("tbody").append('<tr class="emptyInfo"><td colspan="' + t.length + '">' + a + "</td></tr>")
    }

    function d(t, a, o, i) {
        t.find("tr:not(." + o + ")").each(function() {
            var t = e(this),
                o = a.data("index"),
                n = parseInt(t.data("index"), 0),
                r = n + i;
            n >= o && this !== a.get(0) && t.attr("data-index", r).data("index", r)
        })
    }

    function s() {
        function t(t, a, o) {
            var i = e("<td>");
            return t.formatter ? i.html(t.formatter(a, i, o)) : i.html(a || ""), i
        }
        var a = this;
        a.name = "Footable Grid", a.init = function(t) {
            var d = t.options.classes.toggle,
                s = t.options.classes.detail,
                f = t.options.grid;
            if (f.cols) {
                a.footable = t;
                var u = e(t.table);
                u.data("grid", a), e.isPlainObject(f.data) && u.data(f.data), a._items = [], r(f.cols), f.showCheckbox && (f.multiSelect = !0, f.cols.unshift({
                    title: f.checkboxFormatter(!0),
                    name: "",
                    data: {
                        "sort-ignore": !0
                    },
                    formatter: f.checkboxFormatter
                })), f.showIndex && f.cols.unshift({
                    title: "#",
                    name: "index",
                    data: {
                        "sort-ignore": !0
                    },
                    formatter: f.indexFormatter
                }), o(u, f), i(u), n(u, f.cols, f.pagination), u.off(".grid").on({
                    "footable_initialized.grid": function() {
                        f.url || f.ajax ? e.ajax(f.ajax || {
                            url: f.url
                        }).then(function(e) {
                            a.newItem(e), t.raise(f.events.loaded)
                        }, function() {
                            throw "load data from " + (f.url || f.ajax.url) + " fail"
                        }) : (a.newItem(f.items || []), t.raise(f.events.loaded))
                    },
                    "footable_sorted.grid footable_grid_created.grid footable_grid_removed.grid": function() {
                        f.showIndex && a.getItem().length > 0 && u.find("tbody tr:not(." + s + ")").each(function(t) {
                            var a = e(this).find("td:first");
                            a.html(f.indexFormatter(null, a, t))
                        })
                    },
                    "footable_redrawn.grid footable_row_removed.grid": function() {
                        0 === a.getItem().length && f.showEmptyInfo && l(u, f.cols, f.emptyInfo)
                    }
                }).on({
                    "click.grid": function(a) {
                        if (e(a.target).closest("td").find(">." + d).size() > 0) return !0;
                        var o = e(a.currentTarget);
                        return o.hasClass(s) ? !0 : (f.multiSelect || o.hasClass(f.activeClass) || u.find("tbody tr." + f.activeClass).removeClass(f.activeClass), o.toggleClass(f.activeClass), f.showCheckbox && o.find("input:checkbox.check").prop("checked", function(e, t) {
                            return a.target === this ? t : !t
                        }), t.toggleDetail(o), undefined)
                    }
                }, "tbody tr").on("click.grid", "thead input:checkbox.checkAll", function(e) {
                    var t = !!e.currentTarget.checked;
                    t ? u.find("tbody tr").addClass(f.activeClass) : u.find("tbody tr").removeClass(f.activeClass), u.find("tbody input:checkbox.check").prop("checked", t)
                })
            }
        }, a.getSelected = function() {
            var t = a.footable.options.grid,
                o = e(a.footable.table).find("tbody>tr." + t.activeClass);
            return o.map(function() {
                return e(this).data("index")
            })
        }, a.getItem = function(t) {
            return t !== undefined ? e.isArray(t) ? e.map(t, function(e) {
                return a._items[e]
            }) : a._items[t] : a._items
        }, a._makeRow = function(o, i) {
            var n, r = a.footable.options.grid;
            if (e.isFunction(r.template)) n = e(r.template(e.extend({}, {
                __index: i
            }, o)));
            else {
                n = e("<tr>");
                for (var l = 0, d = r.cols.length; d > l; l++) {
                    var s = r.cols[l];
                    n.append(t(s, o[s.name] || "", i))
                }
            }
            return n.attr("data-index", i), n
        }, a.newItem = function(t, o, i) {
            var n = e(a.footable.table).find("tbody"),
                r = a.footable.options.classes.detail;
            if (n.find("tr.emptyInfo").remove(), e.isArray(t)) {
                for (var l; l = t.pop();) a.newItem(l, o, !0);
                return a.footable.redraw(), a.footable.raise(a.footable.options.grid.events.created, {
                    item: t,
                    index: o
                }), undefined
            }
            if (e.isPlainObject(t)) {
                var s, f = a._items.length;
                if (o === undefined || 0 > o || o > f) s = a._makeRow(t, f++), a._items.push(t), n.append(s);
                else {
                    if (s = a._makeRow(t, o), 0 === o) a._items.unshift(t), n.prepend(s);
                    else {
                        var u = n.find("tr[data-index=" + (o - 1) + "]");
                        a._items.splice(o, 0, t), u.data("detail_created") === !0 && (u = u.next()), u.after(s)
                    }
                    d(n, s, r, 1)
                }
                i || (a.footable.redraw(), a.footable.raise(a.footable.options.grid.events.created, {
                    item: t,
                    index: o
                }))
            }
        }, a.setItem = function(t, o) {
            if (e.isPlainObject(t)) {
                var i = e(a.footable.table).find("tbody"),
                    n = a._makeRow(t, o);
                e.extend(a._items[o], t);
                var r = i.find("tr").eq(o);
                r.html(n.html()), a.footable.redraw(), a.footable.raise(a.footable.options.grid.events.updated, {
                    item: t,
                    index: o
                })
            }
        }, a.removeItem = function(t) {
            var o = e(a.footable.table).find("tbody"),
                i = a.footable.options.classes.detail,
                n = [];
            if (e.isArray(t)) {
                for (var r; r = t.pop();) n.push(a.removeItem(r));
                return a.footable.raise(a.footable.options.grid.events.removed, {
                    item: n,
                    index: t
                }), n
            }
            if (t === undefined) o.find("tr").each(function() {
                n.push(a._items.shift()), a.footable.removeRow(this)
            });
            else {
                var l = o.find("tr[data-index=" + t + "]");
                n = a._items.splice(t, 1)[0], a.footable.removeRow(l), d(o, l, i, -1)
            }
            return a.footable.raise(a.footable.options.grid.events.removed, {
                item: n,
                index: t
            }), n
        }
    }
    if (t.footable === undefined || null === t.foobox) throw Error("Please check and make sure footable.js is included in the page and is loaded prior to this script.");
    var f = {
        grid: {
            enabled: !0,
            data: null,
            template: null,
            cols: null,
            items: null,
            url: null,
            ajax: null,
            activeClass: "active",
            multiSelect: !1,
            showIndex: !1,
            showCheckbox: !1,
            showEmptyInfo: !1,
            emptyInfo: '<p class="text-center text-warning">No Data</p>',
            pagination: {
                "page-size": 20,
                "pagination-class": "pagination pagination-centered"
            },
            indexFormatter: function(e, t, a) {
                return a + 1
            },
            checkboxFormatter: function(e) {
                return '<input type="checkbox" class="' + (e ? "checkAll" : "check") + '">'
            },
            events: {
                loaded: "footable_grid_loaded",
                created: "footable_grid_created",
                removed: "footable_grid_removed",
                updated: "footable_grid_updated"
            }
        }
    };
    t.footable.plugins.register(s, f)
})(jQuery, window);;
(function(t, e, undefined) {
    function a() {
        var e = this;
        e.name = "Footable Filter", e.init = function(a) {
            if (e.footable = a, a.options.filter.enabled === !0) {
                if (t(a.table).data("filter") === !1) return;
                a.timers.register("filter"), t(a.table).unbind(".filtering").bind({
                    "footable_initialized.filtering": function() {
                        var i = t(a.table),
                            o = {
                                input: i.data("filter") || a.options.filter.input,
                                timeout: i.data("filter-timeout") || a.options.filter.timeout,
                                minimum: i.data("filter-minimum") || a.options.filter.minimum,
                                disableEnter: i.data("filter-disable-enter") || a.options.filter.disableEnter
                            };
                        o.disableEnter && t(o.input).keypress(function(t) {
                            return window.event ? 13 !== window.event.keyCode : 13 !== t.which
                        }), i.bind("footable_clear_filter", function() {
                            t(o.input).val(""), e.clearFilter()
                        }), i.bind("footable_filter", function(t, a) {
                            e.filter(a.filter)
                        }), t(o.input).keyup(function(i) {
                            a.timers.filter.stop(), 27 === i.which && t(o.input).val(""), a.timers.filter.start(function() {
                                var a = t(o.input).val() || "";
                                e.filter(a)
                            }, o.timeout)
                        })
                    },
                    "footable_redrawn.filtering": function() {
                        var i = t(a.table),
                            o = i.data("filter-string");
                        o && e.filter(o)
                    }
                }).data("footable-filter", e)
            }
        }, e.filter = function(a) {
            var i = e.footable,
                o = t(i.table),
                n = o.data("filter-minimum") || i.options.filter.minimum,
                r = !a,
                l = i.raise("footable_filtering", {
                    filter: a,
                    clear: r
                });
            if (!(l && l.result === !1 || l.filter && n > l.filter.length))
                if (l.clear) e.clearFilter();
                else {
                    var d = l.filter.split(" ");
                    o.find("> tbody > tr").hide().addClass("footable-filtered");
                    var s = o.find("> tbody > tr:not(.footable-row-detail)");
                    t.each(d, function(t, e) {
                        e && e.length > 0 && (o.data("current-filter", e), s = s.filter(i.options.filter.filterFunction))
                    }), s.each(function() {
                        e.showRow(this, i), t(this).removeClass("footable-filtered")
                    }), o.data("filter-string", l.filter), i.raise("footable_filtered", {
                        filter: l.filter,
                        clear: !1
                    })
                }
        }, e.clearFilter = function() {
            var a = e.footable,
                i = t(a.table);
            i.find("> tbody > tr:not(.footable-row-detail)").removeClass("footable-filtered").each(function() {
                e.showRow(this, a)
            }), i.removeData("filter-string"), a.raise("footable_filtered", {
                clear: !0
            })
        }, e.showRow = function(e, a) {
            var i = t(e),
                o = i.next(),
                n = t(a.table);
            i.is(":visible") || (n.hasClass("breakpoint") && i.hasClass("footable-detail-show") && o.hasClass("footable-row-detail") ? (i.add(o).show(), a.createOrUpdateDetailRow(e)) : i.show())
        }
    }
    if (e.footable === undefined || null === e.footable) throw Error("Please check and make sure footable.js is included in the page and is loaded prior to this script.");
    var i = {
        filter: {
            enabled: !0,
            input: ".footable-filter",
            timeout: 300,
            minimum: 2,
            disableEnter: !1,
            filterFunction: function() {
                var e = t(this),
                    a = e.parents("table:first"),
                    i = a.data("current-filter").toUpperCase(),
                    o = e.find("td").text();
                return a.data("filter-text-only") || e.find("td[data-value]").each(function() {
                    o += t(this).data("value")
                }), o.toUpperCase().indexOf(i) >= 0
            }
        }
    };
    e.footable.plugins.register(a, i)
})(jQuery, window);;
(function(e, t, undefined) {
    function a(t) {
        var a = e(t.table),
            i = a.data();
        this.pageNavigation = i.pageNavigation || t.options.pageNavigation, this.pageSize = i.pageSize || t.options.pageSize, this.firstText = i.firstText || t.options.firstText, this.previousText = i.previousText || t.options.previousText, this.nextText = i.nextText || t.options.nextText, this.lastText = i.lastText || t.options.lastText, this.limitNavigation = parseInt(i.limitNavigation || t.options.limitNavigation || o.limitNavigation, 10), this.limitPreviousText = i.limitPreviousText || t.options.limitPreviousText, this.limitNextText = i.limitNextText || t.options.limitNextText, this.limit = this.limitNavigation > 0, this.currentPage = i.currentPage || 0, this.pages = [], this.control = !1
    }

    function i() {
        var t = this;
        t.name = "Footable Paginate", t.init = function(a) {
            if (a.options.paginate === !0) {
                if (e(a.table).data("page") === !1) return;
                t.footable = a, e(a.table).unbind(".paging").bind({
                    "footable_initialized.paging footable_row_removed.paging footable_redrawn.paging footable_sorted.paging footable_filtered.paging": function() {
                        t.setupPaging()
                    }
                }).data("footable-paging", t)
            }
        }, t.setupPaging = function() {
            var i = t.footable,
                o = e(i.table).find("> tbody");
            i.pageInfo = new a(i), t.createPages(i, o), t.createNavigation(i, o), t.fillPage(i, o, i.pageInfo.currentPage)
        }, t.createPages = function(t, a) {
            var i = 1,
                o = t.pageInfo,
                n = i * o.pageSize,
                r = [],
                l = [];
            o.pages = [];
            var d = a.find("> tr:not(.footable-filtered,.footable-row-detail)");
            d.each(function(e, t) {
                r.push(t), e === n - 1 ? (o.pages.push(r), i++, n = i * o.pageSize, r = []) : e >= d.length - d.length % o.pageSize && l.push(t)
            }), l.length > 0 && o.pages.push(l), o.currentPage >= o.pages.length && (o.currentPage = o.pages.length - 1), 0 > o.currentPage && (o.currentPage = 0), 1 === o.pages.length ? e(t.table).addClass("no-paging") : e(t.table).removeClass("no-paging")
        }, t.createNavigation = function(a) {
            var i = e(a.table).find(a.pageInfo.pageNavigation);
            if (0 === i.length) {
                if (i = e(a.pageInfo.pageNavigation), i.parents("table:first").length > 0 && i.parents("table:first") !== e(a.table)) return;
                i.length > 1 && a.options.debug === !0 && console.error("More than one pagination control was found!")
            }
            if (0 !== i.length) {
                i.is("ul") || (0 === i.find("ul:first").length && i.append("<ul />"), i = i.find("ul")), i.find("li").remove();
                var o = a.pageInfo;
                o.control = i, o.pages.length > 0 && (i.append('<li class="footable-page-arrow"><a data-page="first" href="#first">' + a.pageInfo.firstText + "</a>"), i.append('<li class="footable-page-arrow"><a data-page="prev" href="#prev">' + a.pageInfo.previousText + "</a></li>"), o.limit && i.append('<li class="footable-page-arrow"><a data-page="limit-prev" href="#limit-prev">' + a.pageInfo.limitPreviousText + "</a></li>"), o.limit || e.each(o.pages, function(e, t) {
                    t.length > 0 && i.append('<li class="footable-page"><a data-page="' + e + '" href="#">' + (e + 1) + "</a></li>")
                }), o.limit && (i.append('<li class="footable-page-arrow"><a data-page="limit-next" href="#limit-next">' + a.pageInfo.limitNextText + "</a></li>"), t.createLimited(i, o, 0)), i.append('<li class="footable-page-arrow"><a data-page="next" href="#next">' + a.pageInfo.nextText + "</a></li>"), i.append('<li class="footable-page-arrow"><a data-page="last" href="#last">' + a.pageInfo.lastText + "</a></li>")), i.off("click", "a[data-page]").on("click", "a[data-page]", function(n) {
                    n.preventDefault();
                    var r = e(this).data("page"),
                        l = o.currentPage;
                    if ("first" === r) l = 0;
                    else if ("prev" === r) l > 0 && l--;
                    else if ("next" === r) o.pages.length - 1 > l && l++;
                    else if ("last" === r) l = o.pages.length - 1;
                    else if ("limit-prev" === r) {
                        l = -1;
                        var d = i.find(".footable-page:first a").data("page");
                        t.createLimited(i, o, d - o.limitNavigation), t.setPagingClasses(i, o.currentPage, o.pages.length)
                    } else if ("limit-next" === r) {
                        l = -1;
                        var s = i.find(".footable-page:last a").data("page");
                        t.createLimited(i, o, s + 1), t.setPagingClasses(i, o.currentPage, o.pages.length)
                    } else l = r;
                    if (l >= 0) {
                        if (o.limit && o.currentPage != l) {
                            for (var f = l; 0 !== f % o.limitNavigation;) f -= 1;
                            t.createLimited(i, o, f)
                        }
                        t.paginate(a, l)
                    }
                }), t.setPagingClasses(i, o.currentPage, o.pages.length)
            }
        }, t.createLimited = function(e, t, a) {
            a = a || 0, e.find("li.footable-page").remove();
            var i, o, n = e.find('li.footable-page-arrow > a[data-page="limit-prev"]').parent(),
                r = e.find('li.footable-page-arrow > a[data-page="limit-next"]').parent();
            for (i = t.pages.length - 1; i >= 0; i--) o = t.pages[i], i >= a && a + t.limitNavigation > i && o.length > 0 && n.after('<li class="footable-page"><a data-page="' + i + '" href="#">' + (i + 1) + "</a></li>");
            0 === a ? n.hide() : n.show(), a + t.limitNavigation >= t.pages.length ? r.hide() : r.show()
        }, t.paginate = function(a, i) {
            var o = a.pageInfo;
            if (o.currentPage !== i) {
                var n = e(a.table).find("> tbody"),
                    r = a.raise("footable_paging", {
                        page: i,
                        size: o.pageSize
                    });
                if (r && r.result === !1) return;
                t.fillPage(a, n, i), o.control.find("li").removeClass("active disabled"), t.setPagingClasses(o.control, o.currentPage, o.pages.length)
            }
        }, t.setPagingClasses = function(e, t, a) {
            e.find("li.footable-page > a[data-page=" + t + "]").parent().addClass("active"), t >= a - 1 && (e.find('li.footable-page-arrow > a[data-page="next"]').parent().addClass("disabled"), e.find('li.footable-page-arrow > a[data-page="last"]').parent().addClass("disabled")), 1 > t && (e.find('li.footable-page-arrow > a[data-page="first"]').parent().addClass("disabled"), e.find('li.footable-page-arrow > a[data-page="prev"]').parent().addClass("disabled"))
        }, t.fillPage = function(a, i, o) {
            a.pageInfo.currentPage = o, e(a.table).data("currentPage", o), i.find("> tr").hide(), e(a.pageInfo.pages[o]).each(function() {
                t.showRow(this, a)
            }), a.raise("footable_page_filled")
        }, t.showRow = function(t, a) {
            var i = e(t),
                o = i.next(),
                n = e(a.table);
            n.hasClass("breakpoint") && i.hasClass("footable-detail-show") && o.hasClass("footable-row-detail") ? (i.add(o).show(), a.createOrUpdateDetailRow(t)) : i.show()
        }
    }
    if (t.footable === undefined || null === t.footable) throw Error("Please check and make sure footable.js is included in the page and is loaded prior to this script.");
    var o = {
        paginate: !0,
        pageSize: 10,
        pageNavigation: ".pagination",
        firstText: "&laquo;",
        previousText: "&lsaquo;",
        nextText: "&rsaquo;",
        lastText: "&raquo;",
        limitNavigation: 0,
        limitPreviousText: "...",
        limitNextText: "..."
    };
    t.footable.plugins.register(i, o)
})(jQuery, window);;
(function(t, e, undefined) {
    function a() {
        var e = this;
        e.name = "Footable Sortable", e.init = function(a) {
            e.footable = a, a.options.sort === !0 && t(a.table).unbind(".sorting").bind({
                "footable_initialized.sorting": function() {
                    var i, o, n = t(a.table),
                        r = (n.find("> tbody"), a.options.classes.sort);
                    if (n.data("sort") !== !1) {
                        n.find("> thead > tr:last-child > th, > thead > tr:last-child > td").each(function() {
                            var e = t(this),
                                i = a.columns[e.index()];
                            i.sort.ignore === !0 || e.hasClass(r.sortable) || (e.addClass(r.sortable), t("<span />").addClass(r.indicator).appendTo(e))
                        }), n.find("> thead > tr:last-child > th." + r.sortable + ", > thead > tr:last-child > td." + r.sortable).unbind("click.footable").bind("click.footable", function(a) {
                            a.preventDefault(), o = t(this);
                            var i = !o.hasClass(r.sorted);
                            return e.doSort(o.index(), i), !1
                        });
                        var l = !1;
                        for (var s in a.columns)
                            if (i = a.columns[s], i.sort.initial) {
                                var d = "descending" !== i.sort.initial;
                                e.doSort(i.index, d);
                                break
                            }
                        l && a.bindToggleSelectors()
                    }
                },
                "footable_redrawn.sorting": function() {
                    var i = t(a.table),
                        o = a.options.classes.sort;
                    i.data("sorted") >= 0 && i.find("> thead > tr:last-child > th").each(function(a) {
                        var i = t(this);
                        return i.hasClass(o.sorted) || i.hasClass(o.descending) ? (e.doSort(a), undefined) : undefined
                    })
                },
                "footable_column_data.sorting": function(e) {
                    var a = t(e.column.th);
                    e.column.data.sort = e.column.data.sort || {}, e.column.data.sort.initial = a.data("sort-initial") || !1, e.column.data.sort.ignore = a.data("sort-ignore") || !1, e.column.data.sort.selector = a.data("sort-selector") || null;
                    var i = a.data("sort-match") || 0;
                    i >= e.column.data.matches.length && (i = 0), e.column.data.sort.match = e.column.data.matches[i]
                }
            }).data("footable-sort", e)
        }, e.doSort = function(a, i) {
        	var o = e.footable;
        	
        	var table_id = o.table.id;
            var index = a + 1;
            var direction = i;
            var empty_cells = $("#"+table_id).find('td.empty:nth-child(' + index + ')');
            //renumberScoreTableOrder('#overviewTable', index);
            
            
            if (t(o.table).data("sort") !== !1) {
                var n = t(o.table),
                    r = n.find("> tbody"),
                    l = o.columns[a],
                    s = n.find("> thead > tr:last-child > th:eq(" + a + ")"),
                    d = o.options.classes.sort,
                    f = o.options.events.sort;
                if (i = i === undefined ? s.hasClass(d.sorted) : "toggle" === i ? !s.hasClass(d.sorted) : i, l.sort.ignore === !0) return !0;
                var u = o.raise(f.sorting, {
                    column: l,
                    direction: i ? "ASC" : "DESC"
                });
                u && u.result === !1 || (n.data("sorted", l.index), n.find("> thead > tr:last-child > th, > thead > tr:last-child > td").not(s).removeClass(d.sorted + " " + d.descending), i === undefined && (i = s.hasClass(d.sorted)), i ? s.removeClass(d.descending).addClass(d.sorted) : s.removeClass(d.sorted).addClass(d.descending), e.sort(o, r, l, i), o.bindToggleSelectors(), o.raise(f.sorted, {
                    column: l,
                    direction: i ? "ASC" : "DESC"
                }))
            }
        }, e.rows = function(e, a, i) {
            var o = [];
            return a.find("> tr").each(function() {
                var a = t(this),
                    n = null;
                if (a.hasClass(e.options.classes.detail)) return !0;
                a.next().hasClass(e.options.classes.detail) && (n = a.next().get(0));
                var r = {
                    row: a,
                    detail: n
                };
                return i !== undefined && (r.value = e.parse(this.cells[i.sort.match], i)), o.push(r), !0
            }).detach(), o
        }, e.sort = function(t, a, i, o) {
            var n = e.rows(t, a, i),
                r = t.options.sorters[i.type] || t.options.sorters.alpha;
            n.sort(function(t, e) {
                return o ? r(t.value, e.value) : r(e.value, t.value)
            });
            for (var l = 0; n.length > l; l++) a.append(n[l].row), null !== n[l].detail && a.append(n[l].detail)
        }
    }
    if (e.footable === undefined || null === e.footable) throw Error("Please check and make sure footable.js is included in the page and is loaded prior to this script.");
    var i = {
        sort: !0,
        sorters: {
            alpha: function(t, e) {
                return "string" == typeof t && (t = t.toLowerCase()), "string" == typeof e && (e = e.toLowerCase()), t === e ? 0 : e > t ? -1 : 1
            },
            numeric: function(t, e) {
                return t - e
            }
        },
        classes: {
            sort: {
                sortable: "footable-sortable",
                sorted: "footable-sorted",
                descending: "footable-sorted-desc",
                indicator: "footable-sort-indicator"
            }
        },
        events: {
            sort: {
                sorting: "footable_sorting",
                sorted: "footable_sorted"
            }
        }
    };
    e.footable.plugins.register(a, i)
})(jQuery, window);;
(function(t, e, undefined) {
    function a() {
        var e = this;
        e.name = "Footable Striping", e.init = function(a) {
            e.footable = a, t(a.table).unbind("striping").bind({
                "footable_initialized.striping footable_row_removed.striping footable_redrawn.striping footable_sorted.striping footable_filtered.striping": function() {
                    t(this).data("striping") !== !1 && e.setupStriping(a)
                }
            })
        }, e.setupStriping = function(e) {
            var a = 0;
            t(e.table).find("> tbody > tr:not(.footable-row-detail)").each(function() {
                var i = t(this);
                i.removeClass(e.options.classes.striping.even).removeClass(e.options.classes.striping.odd), 0 === a % 2 ? i.addClass(e.options.classes.striping.even) : i.addClass(e.options.classes.striping.odd), a++
            })
        }
    }
    if (e.footable === undefined || null === e.foobox) throw Error("Please check and make sure footable.js is included in the page and is loaded prior to this script.");
    var i = {
        striping: {
            enabled: !0
        },
        classes: {
            striping: {
                odd: "footable-odd",
                even: "footable-even"
            }
        }
    };
    e.footable.plugins.register(a, i)
})(jQuery, window);;
(function(t, e, undefined) {
    function a(t, e) {
        e = e ? e : location.hash;
        var a = RegExp("&" + t + "(?:=([^&]*))?(?=&|$)", "i");
        return (e = e.replace(/^\#/, "&").match(a)) ? e[1] === undefined ? "" : decodeURIComponent(e[1]) : undefined
    }

    function i(e, a) {
        var i = t(e.table).find("tbody").find("tr:not(.footable-row-detail, .footable-filtered)").length;
        t(e.table).data("status_num_total", i);
        var o = t(e.table).find("tbody").find("tr:not(.footable-row-detail)").filter(":visible").length;
        t(e.table).data("status_num_shown", o);
        var n = t(e.table).data("sorted"),
            r = t(e.table).find("th")[n],
            l = t(r).hasClass("footable-sorted-desc");
        if (t(e.table).data("status_descending", l), e.pageInfo) {
            var s = e.pageInfo.currentPage;
            t(e.table).data("status_pagenum", s)
        }
        var d = "",
            f = t(e.table).data("filter");
        t(f).length && (d = t(f).val()), t(e.table).data("status_filter_val", d);
        var u, p, c;
        if ("footable_row_expanded" == a.type && (u = a.row, u && (p = t(e.table).data("expanded_rows"), c = [], p && (c = p.split(",")), c.push(u.rowIndex), t(e.table).data("expanded_rows", c.join(",")))), "footable_row_collapsed" == a.type && (u = a.row)) {
            p = t(e.table).data("expanded_rows"), c = [], p && (c = p.split(","));
            var g = [];
            for (var b in c)
                if (c[b] == u.rowIndex) {
                    g = c.splice(b, 1);
                    break
                }
            t(e.table).data("expanded_rows", g.join(","))
        }
    }

    function o() {
        var e = this;
        e.name = "Footable LucidBookmarkable", e.init = function(e) {
            e.options.bookmarkable.enabled && t(e.table).bind({
                footable_initialized: function() {
                    var i = e.table.id,
                        o = a(i + "_f"),
                        n = a(i + "_p"),
                        r = a(i + "_s"),
                        l = a(i + "_d"),
                        s = a(i + "_e");
                    if (o) {
                        var d = t(e.table).data("filter");
                        t(d).val(o), t(e.table).trigger("footable_filter", {
                            filter: o
                        })
                    }
                    if (n && t(e.table).data("currentPage", n), r !== undefined) {
                        var f = t(e.table).data("footable-sort"),
                            u = !0;
                        "true" == l && (u = !1), f.doSort(r, u)
                    } else t(e.table).trigger("footable_setup_paging");
                    if (s) {
                        var p = s.split(",");
                        for (var c in p) {
                            var g = t(e.table.rows[p[c]]);
                            g.find("> td:first").trigger("footable_toggle_row")
                        }
                    }
                    e.lucid_bookmark_read = !0
                },
                "footable_page_filled footable_redrawn footable_filtered footable_sorted footable_row_expanded footable_row_collapsed": function(a) {
                    if (i(e, a), e.lucid_bookmark_read) {
                        var o = e.table.id,
                            n = o + "_f",
                            r = o + "_p",
                            l = o + "_s",
                            s = o + "_d",
                            d = o + "_e",
                            f = location.hash.replace(/^\#/, "&"),
                            u = [n, r, l, s, d];
                        for (var p in u) {
                            var c = RegExp("&" + u[p] + "=([^&]*)", "g");
                            f = f.replace(c, "")
                        }
                        var g = {};
                        g[n] = t(e.table).data("status_filter_val"), g[r] = t(e.table).data("status_pagenum"), g[l] = t(e.table).data("sorted"), g[s] = t(e.table).data("status_descending"), g[d] = t(e.table).data("expanded_rows");
                        var b = [];
                        for (var h in g) g[h] !== undefined && b.push(h + "=" + encodeURIComponent(g[h]));
                        f.length && b.push(f), location.hash = b.join("&")
                    }
                }
            })
        }
    }
    if (e.footable === undefined || null === e.foobox) throw Error("Please check and make sure footable.js is included in the page and is loaded prior to this script.");
    var n = {
        bookmarkable: {
            enabled: !1
        }
    };
    e.footable.plugins.register(o, n)
})(jQuery, window);