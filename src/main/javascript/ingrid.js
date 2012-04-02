stubbles.namespace("ajax");

stubbles.ajax.InputGrid = function() {
	this.init();
};

stubbles.ajax.InputGrid.prototype = {

  initToggle: function(toggleElement, showChecked, hideChecked, otherElements) {
    this.setToggleObject(toggleElement, showChecked, hideChecked, otherElements);
    YAHOO.util.Event.addListener(toggleElement, 'click', this.toggle, toggleElement, this);  
  },
  
  toggle: function(object, toggleElement) {
    if (this.isChecked(toggleElement)) {
      this.hide(this.getToggleObject(toggleElement).hideChecked);      
      this.show(this.getToggleObject(toggleElement).showChecked);      
      if (this.isChecked(toggleElement)) {
        document.cookie = toggleElement + '=true';
        if (this.getToggleObject(toggleElement).otherElements instanceof Array) {
          var otherElements = this.getToggleObject(toggleElement).otherElements;
    			for (var i=0; i<otherElements.length; i++) {
            document.cookie = otherElements[i] + '=false';
    			}
        } else {
          document.cookie = this.getToggleObject(toggleElement).otherElements + '=false';
        }
      }
    }
  },

  show: function(elements) {
    if (this.isArray(elements)) {
      for (var i = 0; i < elements.length; i++) {
        if (document.getElementById(elements[i])) {
          YAHOO.util.Dom.setStyle(elements[i], 'display', 'block');
        }
      }      
    } else {
      if (elements && document.getElementById(elements)) {
        YAHOO.util.Dom.setStyle(elements, 'display', 'block');
      }
    }
    this.changeDisabledProperty(elements, false);
  },

  hide: function(elements) {
    if (this.isArray(elements)) {
      for (var i = 0; i < elements.length; i++) {
        if (elements[i] && document.getElementById(elements[i])) {
          YAHOO.util.Dom.setStyle(elements[i], 'display', 'none');
        }
      }      
    } else {
      if (elements && document.getElementById(elements)) {
        YAHOO.util.Dom.setStyle(elements, 'display', 'none');
      }
    }
    this.changeDisabledProperty(elements, true);
  },

  changeDisabledProperty: function(elements, mode) {
    if (elements) {
      if (this.isArray(elements)) {
        for (var i = 0; i < elements.length; i++) {
          if (elements[i] && document.getElementById(elements[i])) {
            inputElements = document.getElementById(elements[i]).getElementsByTagName('input');
            for (var j = 0; j < inputElements.length; j++) {
              inputElements[j].disabled = mode;
            }
          }
        }
      } else {
        if (elements && document.getElementById(elements)) {
          inputElements = document.getElementById(elements).getElementsByTagName('input');
      		for (var j = 0; j < inputElements.length; j++) {
            inputElements[j].disabled = mode;
          }
        }
      }
    }
  },

  initAllToggles: function() {
		var allToggleObjects = this.getAllToggleObjects();
		if (allToggleObjects) {
			for (var toggleElement in allToggleObjects) {
				this.initCheckStatus(toggleElement);
			}
			for (var toggleElement in allToggleObjects) {
				this.toggle(null, toggleElement);
			}
		}
  },
	
  initCheckStatus: function(toggleElement) {
    var checkStatus = null;
    var begin = document.cookie.indexOf(toggleElement + '=');
    if (begin != -1) {
      begin += toggleElement.length + 1;
      checkStatus = document.cookie.substring(begin, document.cookie.indexOf(";", begin));
    }
    if (checkStatus != null) {
      if (checkStatus == 'true') {
        document.getElementById(toggleElement).checked = true;
      } else {
        document.getElementById(toggleElement).checked = false;        
      }
    }
  },
  
  initClearField: function(field) {
    this.setClearField(field);
    YAHOO.util.Event.addListener(field, 'focus', this.clearField, field, this);  
    YAHOO.util.Event.addListener(field, 'blur', this.restoreField, field, this);  
  },
  
  clearField: function(object, field) {
    if (document.getElementById(field).value == this.getClearField(field)) {
      document.getElementById(field).value = '';
    }
  },

  restoreField: function(object, field) {
    if (document.getElementById(field).value == '') {
      document.getElementById(field).value = this.getClearField(field);
    }
  },

	init: function() {

    var toggleObject = new Object();
    var clearField = new Object();

		this.isChecked = function(id) {
			if (document.getElementById(id)) {
				return document.getElementById(id).checked;
			} else {
				return undefined;
			}
		}
		
    this.isArray = function(value) {
      return value &&
        typeof value === 'object' &&
        typeof value.length === 'number' &&
        typeof value.splice === 'function' &&
        !(value.propertyIsEnumerable('length'));
    }
    
		this.getToggleObject = function(id) {
			return toggleObject[id];
		}
		
		this.getAllToggleObjects = function() {
			return toggleObject;
		}
		
		this.setToggleObject = function(id, showChecked, hideChecked, otherElements) {
  		toggleObject[id] = new Object();
			toggleObject[id].showChecked = showChecked;
			toggleObject[id].hideChecked = hideChecked;
			toggleObject[id].otherElements = otherElements;
		}

		this.getClearField = function(id) {
			return clearField[id];
		}
		
		this.setClearField = function(id) {
  		clearField[id] = document.getElementById(id).value;
		}
  }
};

stubbles.ajax.InfoBox = function() {
	this.init();
};

stubbles.ajax.InfoBox.prototype = {

	initInfoBoxMagic: function(id, dimension) {
    var infoDiv = document.getElementById(id).parentNode;
    if (!document.getElementById('iframe.' + id)) {
      var infoBoxIFrame = document.createElement('iframe');
      infoBoxIFrame.setAttribute('id', 'iframe.' + id);
      infoBoxIFrame.className = 'infoBoxMagic';
      infoDiv.appendChild(infoBoxIFrame);
      YAHOO.util.Dom.setStyle('iframe.' + id, 'opacity', 0);
      YAHOO.util.Dom.setStyle('iframe.' + id, 'position', 'absolute');
      YAHOO.util.Dom.setStyle('iframe.' + id, 'display', 'none');
    }
    var infoX = YAHOO.util.Dom.getX(infoDiv);
    var infoY = YAHOO.util.Dom.getY(infoDiv);
    var currentNode = infoDiv;
    while (!(currentNode.nodeName == 'UL')) {
      currentNode = currentNode.parentNode;
    } 
    currentNode.parentNode.appendChild(infoDiv);
    YAHOO.util.Dom.setX(infoDiv, infoX);
    YAHOO.util.Dom.setY(infoDiv, infoY);
		YAHOO.util.Event.addListener(id, 'mouseover', this.handleInfoBoxMouseOver, {id: id, dimension: dimension}, this);
		YAHOO.util.Event.addListener(id, 'mouseout', this.handleInfoBoxMouseOut, {id: id}, this);
	},

	handleInfoBoxMouseOver: function(e, obj) {
		if (obj.id && obj.dimension) {
			this.modifyInfoBoxHover(obj.id, obj.dimension[0], obj.dimension[1], obj.dimension[2], obj.dimension[3], obj.dimension[4]);
		}
	},
	
	handleInfoBoxMouseOut: function(e, obj) {
		if (obj.id) {
			this.resetInfoBox(obj.id);
		}
	},

	modifyInfoBoxHover: function(id, zIndex, width, height, left, top) {
		this.resetInfoBox(this.getLastInfoBox());
		this.setLastInfoBox(id);
		this.modifyBox(id, zIndex, width, height, left, top);
	},
	
	resetInfoBox: function(id) {
		this.modifyBox(id, 5, 16, 16, 0, 0);    
    if (document.getElementById('iframe.' + id)) {
      this.modifyBox('iframe.' + id, 4, 16, 16, 0, 0);
      YAHOO.util.Dom.setStyle('iframe.' + id, 'display', 'none');
    }
	},

	modifyBox: function(id, zIndex, width, height, left, top) {
		if (document.getElementById(id)) {
		  document.getElementById(id).style.zIndex = zIndex;
		  document.getElementById(id).style.width = width + "px";
		  document.getElementById(id).style.height = height + "px";
		  document.getElementById(id).style.left = left + "px";
		  document.getElementById(id).style.top = top + "px";
      if (document.getElementById('iframe.' + id)) {
        YAHOO.util.Dom.setStyle('iframe.' + id, 'display', 'block');
        document.getElementById('iframe.' + id).style.zIndex = zIndex - 1;
        document.getElementById('iframe.' + id).style.width = width + "px";
        document.getElementById('iframe.' + id).style.height = height + "px";
        document.getElementById('iframe.' + id).style.left = left + "px";
        document.getElementById('iframe.' + id).style.top = top + "px";
      }
		}
	},
  
  init: function() {

    var lastInfoBox = null;

	  this.getLastInfoBox = function() {
			return lastInfoBox;
	  };

	  this.setLastInfoBox = function(value) {
			lastInfoBox = value;
	  };
  }

};	
