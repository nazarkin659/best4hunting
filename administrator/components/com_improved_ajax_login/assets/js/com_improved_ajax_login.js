;(function($, undefined) {

var Selected = $(),
    UnSelect = null;
$.widget("gi.selectable", {
  options: {
    className: "gi-selected",
    selected: false,
    select: null
  },

  _create: function() {
    this.element.addClass(this.widgetFullName);
    if (this.options.select)
      this.element.on(
        "mousedown"+this.eventNamespace+" touchstart"+this.eventNamespace,
        $.proxy(this, "triggerSelect"));
    if (this.options.selected) this.triggerSelect();
  },

  triggerSelect: function(e) {
    var active = document.activeElement;
    Selected.removeClass(Selected.selectable("option", "className"));
    Selected = this.element.addClass(this.options.className);
    this._trigger("select", e, Selected);
    if (active && active.blur) active.blur();
  },

  unSelect: function() {
    this.element.removeClass(this.options.className);
    this.options.selected = false;
    if (UnSelect) UnSelect(Selected);
    Selected = $();
  }
});
$.extend($.fn.selectable, {
  getSelected: function() {
    return Selected;
  },

  removeSelection: function(e) {
    if (e) {
      var node = e.target;
      while (node.parentNode) {
        if ($(node).hasClass("gi-selectable")) return;
        else node = node.parentNode;
      }
    }
    Selected.selectable("unSelect");
  },

  onUnSelect: function(eHandler) {
    UnSelect = eHandler;
  }
});

JForm = {
  save: function() {
    var elems = {page: [ {elem: []} ]},
        props = {};
    // save props
    props.layout = $(document.layoutForm).jformObject().toObject();
    $("#jform_props").val(JSON.stringify(props));
    // save fields
    $("#design-layer .gi-elem").each(function() {
      elems.page[0].elem.push($(this).data("ialElem").jfo.toObject());
    });
    $("#jform_fields").val(JSON.stringify(elems));
  },

  load: function() {
    var layer = $("#design-layer"),
        elems = $.parseJSON($("#jform_fields").val()),
        props = $.parseJSON($("#jform_props").val());
    if (props.layout) {
      $(document.layoutForm).jformObject(props.layout);
    }
    if (elems.page) {
      var i, elem, jfo, type;
      elems = elems.page[0].elem;
      for (i = 0; i < elems.length; i++) {
        elem = $('<div />').appendTo(layer);
        jfo = new JFormObject(elems[i]);
        jfo.prefix = "jform[elem_";
        jfo.suffix = "]";
        type = jfo.get("type");
        if (type.predefined) {
          elem.addClass("ui-draggable-disabled");
          elem.attr("data-elem", type.predefined);
        } else elem.attr("data-elem", type.value);
        // make saved element properties compatible with updates
        elem.prop("jfo", $.extend(true, new JFormObject(
          PredefinedElems[elem.data("elem")], "jform[elem_", "]"), jfo));
      }
    }
  }
};

(JFormObject = function(obj, prefix, suffix) {
  if (typeof obj === "object") {
    var key, clone = $.extend(true, {}, obj);
    if (prefix) this.prefix = prefix;
    if (suffix) this.suffix = suffix;
    for (key in clone) this[this.prefix + key + this.suffix] = clone[key];
  }
  return this;
}).prototype = {
  prefix: "",
  suffix: "",

  get: function(key) {
    var value = this[this.prefix + key + this.suffix];
    return value? value : "";
  },

  toObject: function() {
    var key, obj = {};
    for (key in this)
      if (this.__proto__[key] === undefined) obj[key] = this[key];
    return obj;
  }
};

function disable(elem, disabled) {
  if (elem.type == "hidden") return;
  elem.parentNode.parentNode.style.display = disabled? "none" : "table-row";
}

$.fn.jformObject = function(obj) {
  var elems = this.length? this[0].elements : [],
      elem, name, value, i;

  if (obj === undefined || $.isArray(obj)) {
    // getter
    var jfo = new JFormObject();
    if (obj) for (i = 0; i < obj.length; i++) {
      elem = elems[ obj[i] ];
      if (!elem.name) for (var j = 0; j < elem.length; j++) {
        if (elem[j].checked) {
          jfo[ elem[j].name ] = elem[j].value;
          break;
        }
      } else jfo[elem.name] = elem.value;
    } else for (i = 0; i < elems.length; i++) {
      elem = elems[i];
      if (elem.type == "radio" && !elem.checked || !elem.name) continue;
      jfo[elem.name] = elem.value;
    }
    return jfo;
  } else {
    // setter
    for (i = 0; i < elems.length; i++) {
      elem = elems[i];
      if (elem.name) {
        // text, textarea, checkbox, hidden
        name = elem.name;
        if (obj[name]) {
          var $elem = $(elem);
          if (obj[name].checked !== undefined) delete obj[name].value;
          if (elem.checked) $elem.removeAttr("checked");
          if (typeof obj[name] === "object") $elem.attr(obj[name]);
          else $elem.val(obj[name]);
          $elem.trigger("change");
        }
      } else if (elem.tagName.toLowerCase() == "fieldset") {
        // radio
        name = elems[i+1].name;
        value = obj[name].value? obj[name].value : obj[name];
        do {
          i++;
          if (!obj[name]) continue;
          var $radio = $(elems[i]);
          if (elems[i].value === value) $radio.attr("checked", true);
        } while (elems[i+1].name == name);
      }
      disable(elem, obj[name] === undefined);
    }
    return this;
  }
};

$.fn.elem = function(name, value, placeholder) {
  return this.each(function() {
    var $elem, $this = $(this);
    if (name === undefined) {
      // constructor
      var jfo = $this.prop("jfo");
      if (!jfo) jfo = new JFormObject(
        PredefinedElems[$this.attr("data-elem")], "jform[elem_", "]");
      var type = jfo.get("type"), plg = type.value;
      $this.html(
        '<span class="btn gi-elem-name">'+
          '<i class="'+type.icon+'"></i> '+ type.button+
        '</span>');
      $elem = $('<div data-attr="wide" />').appendTo($this);
      plg = "ial" + plg.charAt(0).toUpperCase() + plg.slice(1);
      $elem[plg]({jfo: jfo});
      if (jfo.get("wide").checked) $this.addClass("gi-wide"); // layout fix
      return;
    }
    if (value !== undefined) {
      // setter
      $elem = $this.children(".gi-elem");
      $elem.ialElem("setAttr", name, value, placeholder || "");
      if (name == "jform[elem_wide]")
        $this[value? "addClass" : "removeClass"]("gi-wide"); // layout fix
    }
  });
};

$.createOOPlugin("giListOpt", {
  className: "ial-opt",
  list: undefined,

  Constructor: function(params) {
    $.extend(this, params);
    this.$node.addClass(this.className);
    this.$chk = $('<input type="radio" />')
      .attr({checked: this.args[0], name: this.list.id})
      .appendTo(this.$node)
      .on("change", $.proxy(this.list, "refresh"));
    this.$opt = $('<input type="text" placeholder="Option" title="Option" />')
      .val(this.args[1])
      .appendTo(this.$node)
      .on("keyup", $.proxy(this, "onKeyUp"))
      .on("blur", $.proxy(this.list, "refresh"));
    this.$val = $('<input type="text" placeholder="Value" title="Value" />')
      .val(this.args[2])
      .appendTo(this.$node)
      .on("blur", $.proxy(this.list, "refresh"));
    $('<a href="javascript:;" class="icon-plus" title="Add" />')
      .appendTo(this.$node).on("click", $.proxy(this.list, "onAdd"));
    $('<a href="javascript:;" class="icon-trash" title="Delete" />')
      .appendTo(this.$node).on("click", $.proxy(this.list, "onDel"));
    delete this.args;
  },

  html: function() {
    var opt = this.$opt.val();
    return '[option value="'+(this.$val.val() || opt)+'"'+
      (this.$chk.attr("checked")? ' selected="selected"]' : ']')+
      opt+'[/option]';
  },

  onKeyUp: function() {
    this.$val.attr("placeholder", this.$opt.val() || "Value");
    if (this.$chk.attr("checked"))
      $.fn.selectable.getSelected()
        .find("option:selected").html(this.$opt.val());
  }
});

$.createOOPlugin("giList", {
  Constructor: function(params) {
    $.extend(this, params);
    this.$opts = $('<div class="ial-opts" />').insertAfter(this.$node);
    this.$node.css("display", "none")
      .on("change", $.proxy(this, "initOpts"));
  },

  initOpts: function() {
    var opt, i,
        $opts = $(this.$node.val().replace(/\[/g, "<").replace(/\]/g, ">"));
    this.$opts.html("");
    if ($opts.length) for (i = 0; i < $opts.length; i++) {
      opt = $opts[i];
      this.addOpt(opt.selected, opt.innerHTML, opt.value);
    } else this.addOpt(true, "", "");
  },

  refresh: function() {
    var html = "";
    this.$opts.children().each(function() {
      html += $(this).giListOpt("html");
    });
    this.$node.val(html).trigger("keyup");
  },

  addOpt: function(chk, opt, val, after) {
    return $('<div />').giListOpt({
      list: this,
      args: arguments
    })[after? "insertAfter" : "appendTo"](after || this.$opts);
  },

  onAdd: function(e) {
    this.addOpt(false, "", "", e.currentTarget.parentNode);
  },

  onDel: function(e) {
    $(e.currentTarget.parentNode).remove();
    if (!this.$opts.children().length) this.addOpt(true, "", "");
    if (!$("input:checked", this.$opts).length)
      $("input:first", this.$opts).attr("checked", true);
    this.refresh();
  }
});

$.createOOPlugin("giMsg", {
  Constructor: function(params) {
    $.extend(this, params);
    this.$node
      .on("focus", $.proxy(this, "onFocus"))
      .on("keyup", $.proxy(this, "onKeyUp"))
      .on("blur", $.proxy(this, "onBlur"));
  },

  onFocus: function() {
    var $input = $.fn.selectable.getSelected().find(":input");
    if ($input.prop("type") == "checkbox") $input = $input.parent();
    $input.ialErrorMsg({
        pos: "r",
        ico: this.ico,
        msg: this.$node.val() || this.$node.attr("placeholder") || "Message"
      });
    this.ialMsg = $input.data("ialErrorMsg");
    this.msg = this.ialMsg.$msg.find(".ial-icon-"+this.ico)[0].nextSibling;
    $input.removeData("ialErrorMsg");
  },

  onKeyUp: function() {
    this.msg.textContent = this.$node.val()
      || this.$node.attr("placeholder") || "Message"
  },

  onBlur: function() {
    this.ialMsg.hide();
  }
});

})(jQuery);
jQuery(function($) {

window.ologin = {
  base: JURI,
  showHint: false,
  captcha: "6Lc8m9USAAAAAPmbY8EiK9eVXKClTwNqSsqK6TGZ"
};

var delBtn = $("#delete-btn"),
    formTab = $("#form-tab"),
    elemTab = $("#elem-tab"),
    prop = $(".gi-properties"),
    designLayer = $("#design-layer"),
    adminForm = $(document.adminForm),
    layoutForm = $(document.layoutForm),
    elemForm = $(document.elemForm),
    initialized = init();

function init() {
  // IE fix
  isIE = navigator.userAgent.match(/MSIE (\d+)/);
  if (isIE) $(document.body).addClass('gi-ie-'+isIE[1]);
  // load saved fields and properties
  JForm.load();
  // init layout
  onChangeLayoutProp();
  // init fields
  $("[data-elem]").elem();
  // init accordion menus
  $(".ui-accordion").accordion({
    heightStyle: "content",
    animate: 250
  });
  // init draggable elements
	$(".ui-draggable").draggable({
		connectToSortable: designLayer,
    revert: false,
    cancel: null,
		helper: function() {
      var $hlp = $(this).clone();
      $hlp.find(".gi-elem-name").css("display", "none");
      $hlp.find(".gi-elem").css("display", "block");
      $hlp.addClass("gi-move");
      $.fn.selectable.removeSelection();
      return $hlp;
    }
	}).addClass("gi-selectable");
  // disable predefined elements which are in use
  $("[data-elem]", designLayer).each(function() {
    var predefined = this.jfo.get("type").predefined;
    if (predefined) {
      this.predefined = $("[data-elem="+predefined+"]:first")
        .draggable("disable");
      $(".gi-elem-name", this.predefined).addClass("disabled");
    }
  });
  // init dropable and sortable elements
  designLayer.droppable({
    drop: function(e, ui) {
      this.$dropped = ui.draggable;
    }
  }).sortable({
    revert: 333,
    cursor: "move",
    cancel: null,
    receive: onReceiveSortable
	}).disableSelection()
    .parent().addClass("gi-"+Theme);
  // init selectable elements
  designLayer.children().selectable({select: onSelect});
  designLayer.on("mousedown touchstart", $.fn.selectable.removeSelection);
  $.fn.selectable.onUnSelect(onUnSelect);
  // init events
  delBtn.on("click", onClickDelBtn);
  $(document).on("keypress", onKeyPressDocument);
  $("#jform_layout_columns").on("click", onChangeLayoutProp);
  $("input[type=text]", layoutForm).on("change", onChangeLayoutProp);
  $("input[type=text]", layoutForm).on("focus", onFocusLayoutProp);
  $("input[type=text], textarea", elemForm).on("keyup", onChangeElemProp);
  $("input[type=checkbox]", elemForm).on("change", onChangeElemProp);
  $("input[type=hidden]", elemForm).on("change", onChangeElemProp);
  // init special params
  $(".gi-list").giList();
  $(".gi-title").giMsg({ico: "inf"});
  $(".gi-error").giMsg({ico: "err"});
  // disable chosen on J!3.x
  $(function($) {
    $("select.chzn-done")
      .removeClass("chzn-done")
      .removeAttr("style")
      .removeData("chosen")
      .next().remove();
  });
  return true;
}

function onReceiveSortable(e, ui) {
  var $elem = this.$dropped.children(".gi-elem").html(""),
      elem = this.$dropped.data("elem"),
      jfo = new JFormObject(PredefinedElems[elem], "jform[elem_", "]"),
      plg = jfo.get("type").value;
  this.$dropped.prop("jfo", jfo);
  this.$dropped.selectable({
    selected: true,
    select: onSelect
  });
  plg = "ial" + plg.charAt(0).toUpperCase() + plg.slice(1);
  $elem[plg]({jfo: jfo});
  //#hernyókisfanni## xoxo gossipgilr <3123.4
  if (jfo.get("type").predefined) {
    this.$dropped.prop("predefined", ui.item.draggable("disable"));
    $(".gi-elem-name", ui.item).addClass("disabled");
  }
}

function onSelect(e, ui) {
  var jfo = ui.prop("jfo");
  elemForm.jformObject(jfo);
  delBtn.removeClass("disabled");
  elemTab.parent().removeClass("hidden");
  elemTab.tab("show");
}

function onUnSelect(ui) {
  delBtn.addClass("disabled");
  elemTab.parent().addClass("hidden");
  formTab.tab("show");
}

function onClickDelBtn() {
  var selected = $.fn.selectable.getSelected();
  if (selected.length && confirm("Are you sure you want to delete?")) {
    var predefined = selected.prop("predefined");
    if (predefined) {
      $(".gi-elem-name", predefined).removeClass("disabled");
      predefined.draggable("enable");
    }
    $.fn.selectable.removeSelection();
    selected.selectable("destroy").animate({
      opacity: 0,
      height: 0
    }, 300, "swing", $.proxy(selected, "remove"));
  }
}

function onChangeElemProp(e) {
  var target = e.currentTarget,
      checkbox = target.type == "checkbox";
  $.fn.selectable.getSelected().elem(
    target.name,
    checkbox? target.checked : target.value,
    checkbox? "CHK" : target.placeholder
  );
}

function onChangeLayoutProp(e) {
  var lColumn = $("#jform_layout_columns :checked").val(),
      lWidth = parseInt($("#jform_layout_width").val()),
      lMargin = parseInt($("#jform_layout_margin").val()),
      d1 = 0, d2 = 0;
  if (e && e.currentTarget.prevValue) {
    var input = e.currentTarget;
    if (isNaN(parseInt(input.value))) input.value = input.prevValue;
    if (parseInt(input.prevValue) > parseInt(input.value)) d1 = 33;
    else d2 = 33;
    input.value = parseInt(input.value)+"px";
  }
  jss("#design-layer", {
    width: lColumn*(lWidth + 2*lMargin) + "px",
    WebkitTransitionDelay: d1 + "ms",
    transitionDelay: d1 + "ms"
  });
  jss(".gi-elem", {
    width: lWidth + "px",
    margin: "0 " + lMargin + "px",
    WebkitTransitionDelay: d2 + "ms",
    transitionDelay: d2 + "ms"
  });
}

function onFocusLayoutProp(e) {
  e.currentTarget.prevValue = e.currentTarget.value;
}

function onKeyPressDocument(e) {
  switch (e.keyCode) {
    case 13:  // enter
      if (e.target.type == "text") e.target.blur();
      return;
    case 46:  // delete
      if (e.target == document.body) onClickDelBtn();
      return;
  }
}

});