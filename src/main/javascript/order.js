stubbles.namespace("ajax");

stubbles.ajax.OrderProcess = function() {
	this.init();
};

stubbles.ajax.OrderProcess.prototype = {

  hideWrapperWarnings: function() {
    var noWrapperElements = this.getElementsByClassName('nowrapper');
    for (var i = 0; i < noWrapperElements.length; i++) {
  		var allDivElements = noWrapperElements[i].getElementsByTagName("div");
  		var regexp = new RegExp("Warning: Unknown wrapper");
  		for (var j = 0; j < allDivElements.length; j++) {
  			if (allDivElements[j].firstChild) {
  				if (regexp.test(allDivElements[j].firstChild.nodeValue)) {
  					allDivElements[j].style.display = 'none';
  				}
  			}
  		}
    }
  },

  initTooltipEffect: function() {
    var tooltipElements = this.getElementsByClassName('tooltip');
    for (var i = 0; i < tooltipElements.length; i++) {
      YAHOO.util.Event.addListener(tooltipElements[i], 'mouseover', this.showTooltip, this);     
      YAHOO.util.Event.addListener(tooltipElements[i], 'mousemove', this.moveTooltip, this);     
      YAHOO.util.Event.addListener(tooltipElements[i], 'mouseout', this.hideTooltip, this);     
    }
  },

  showTooltip: function(ev, scope) {
    var textElement = scope.getElementsByClassName('tooltip-text', ev.currentTarget);
    YAHOO.util.Dom.setStyle(textElement, 'display', 'block'); 
    YAHOO.util.Dom.setStyle(ev.currentTarget, 'cursor', 'help'); 
    YAHOO.util.Dom.setX(textElement, ev.clientX + 15); 
    YAHOO.util.Dom.setY(textElement, ev.clientY + 2); 
  },
  
  moveTooltip: function(ev, scope) {
    var textElement = scope.getElementsByClassName('tooltip-text', ev.currentTarget);
    YAHOO.util.Dom.setX(textElement, ev.clientX + 15); 
    YAHOO.util.Dom.setY(textElement, ev.clientY + 2); 
  },
  
  hideTooltip: function(ev, scope) {
    var textElement = scope.getElementsByClassName('tooltip-text', ev.currentTarget);
    YAHOO.util.Dom.setStyle(textElement, 'display', 'none'); 
  },
  
  getElementsByClassName: function(clsName, element){
    var retVal = new Array();
    var elements;
    if (element) {
      elements = element.getElementsByTagName("*");
    } else {
      elements = document.getElementsByTagName("*");
    }
    for (var i = 0;i < elements.length;i++){
      if (elements[i].className.indexOf(" ") >= 0){
        var classes = elements[i].className.split(" ");
        for (var j = 0;j < classes.length;j++){
          if (classes[j] == clsName)
            retVal.push(elements[i]);
        }
      }
      else if (elements[i].className == clsName)
        retVal.push(elements[i]);
    }
    return retVal;
  },
  
	initHighlightEffect: function() {
    var allHighlightElements = document.getElementsByTagName("input");
		for (var i = 0; i < allHighlightElements.length; i++) {
			if (allHighlightElements[i].type == 'text' || allHighlightElements[i].type == 'password') {
				YAHOO.util.Event.addListener(allHighlightElements[i], "focus", this.switchHighlightOn, this, allHighlightElements[i]);
				YAHOO.util.Event.addListener(allHighlightElements[i], "blur", this.switchHighlightOff, this, allHighlightElements[i]);
			}
		}
	},
	
	switchHighlightOn: function() {
		YAHOO.util.Dom.addClass(this, 'highlight');
	},
	
	switchHighlightOff: function() {
		YAHOO.util.Dom.removeClass(this, 'highlight');
	},
  
  init: function() {
    this.hideWrapperWarnings();
    this.initTooltipEffect();
    this.initHighlightEffect();
  }
  
};