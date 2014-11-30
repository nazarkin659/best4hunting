

    function magicToolTip(toolTipClass) {

        this.toolTipClass = toolTipClass;
        this.toolTipContainer = null;
        this.toolTipContainerId = 'magictooltip';
        this.timer = null;
        this.hidden = true;

        var self = this;
        var delay = 500;

        this.initialize = function() {
            var elements = this.getElementsByClass(this.toolTipClass, document);
            if(elements) {
                if(this.toolTipContainer == null) {
                    this.toolTipContainer = document.createElement('div');
                    this.toolTipContainer.setAttribute('id', this.toolTipContainerId);
                    this.toolTipContainer.style.position = 'absolute';
                    this.toolTipContainer.style.display = 'none';
                    this.toolTipContainer.style.width = '150px';
                    this.toolTipContainer.style.height = 'auto';
                    this.toolTipContainer.style.border = '1px solid #DDDDBB';
                    this.toolTipContainer.style.padding = '4px 4px 4px 8px';
                    this.toolTipContainer.style.background = '#FFFFCC';
                    //this.toolTipContainer.style.opacity = '0.8';
                    //this.toolTipContainer.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(Opacity=80)';
                    if(this.toolTipContainer.addEventListener) {
                        this.toolTipContainer.addEventListener('mouseover', this.toolTipContainerOver, false);
                        this.toolTipContainer.addEventListener('mouseout', this.toolTipContainerOut, false);
                    } else {
                        this.toolTipContainer.attachEvent('onmouseover', this.toolTipContainerOver);
                        this.toolTipContainer.attachEvent('onmouseout', this.toolTipContainerOut);
                    }
                    document.body.appendChild(this.toolTipContainer);
                }
                for(var i = 0; i < elements.length; i++) {
                    var el = elements[i];
                    if(el.getAttribute('title') != undefined) {
                        el.toolTip = el.getAttribute('title');
                        if(el.addEventListener) {
                            el.addEventListener('mouseover', this.showTooTip, false);
                            el.addEventListener('mouseout', this.hideTooTip, false);
                        } else {
                            el.attachEvent('onmouseover', this.showTooTip);
                            el.attachEvent('onmouseout', this.hideTooTip);
                        }
                    }
                }
            }
        }

        this.showTooTip = function(event) {
            var target = (event.target == undefined) ? event.srcElement : event.target;
            target.title = '';//target.setAttribute('title', '');
            if(self.toolTipContainer != null) {
                if(self.timer) {
                    clearTimeout(self.timer);
                    self.timer = null;
                }
                if(!self.hidden) {
                    self.toolTipContainer.style.display = 'none';
                    self.hidden = true;
                }
                //self.toolTipContainer.innerHTML = target.toolTip/*getAttribute('title')*/;
                self.toolTipContainer.innerHTML = target.toolTip.replace(/&amp;/g, '&').replace(/&gt;/g, '>').replace(/&lt;/g, '<').replace(/&quot;/g, '"').replace(/&#34;/g, '"');
                var offset = function(event) {
                    var body = (document.compatMode && document.compatMode != "BackCompat") ? document.documentElement : document.body;
                    return {
                        x: event.clientX + (document.all ? body.scrollLeft : window.pageXOffset),
                        y: event.clientY + (document.all ? body.scrollTop : window.pageYOffset)
                    }
                }(event)
                self.toolTipContainer.style.left = (offset.x + 5) + 'px';
                self.toolTipContainer.style.top = (offset.y + 5) + 'px';
                self.toolTipContainer.style.display = 'block';
                self.hidden = false;
            }
        }

        this.hideTooTip = function(event) {
            var target = (event.target == undefined) ? event.srcElement : event.target;
            target.title = target.toolTip
            if(self.toolTipContainer != null) {
                if(self.timer) {
                    clearTimeout(self.timer);
                }
                self.timer = setTimeout(function() {
                    self.toolTipContainer.style.display = 'none';
                    self.timer = null;
                    self.hidden = true;
                }, delay);
            }
        }

        this.toolTipContainerOver = function(event) {
            var target = (event.target == undefined) ? event.srcElement : event.target;
            var relatedTarget = event.relatedTarget || event.fromElement;
            if(target != self.toolTipContainer) {
                return;
            }
            while(relatedTarget.nodeName.toLowerCase() != 'body') {
                relatedTarget = relatedTarget.parentNode;
                if(relatedTarget == self.toolTipContainer) {
                    return;
                }
            }
            if(self.timer) {
                clearTimeout(self.timer);
                self.timer = null;
            }
        }

        this.toolTipContainerOut = function(event) {
            var target = (event.target == undefined) ? event.srcElement : event.target;
            var relatedTarget = event.relatedTarget || event.toElement;
            if(target != self.toolTipContainer) {
                return;
            }
            while(relatedTarget.nodeName.toLowerCase() != 'body') {
                relatedTarget = relatedTarget.parentNode;
                if(relatedTarget == self.toolTipContainer) {
                    return;
                }
            }
            if(self.timer) {
                clearTimeout(self.timer);
            }
            self.timer = setTimeout(function() {
                self.toolTipContainer.style.display = 'none';
                self.timer = null;
                self.hidden = true;
            }, delay);
        }

        this.getElementsByClass = function(classList, node) {
            var node = node || document;
            if(node.getElementsByClassName) {
                return node.getElementsByClassName(classList);
            } else {
                var nodes = node.getElementsByTagName("*"),
                    nodesLength = nodes.length,
                    classes = classList.split(/\s+/),
                    classesLength = classes.length,
                    result = [];
                for(var i = 0; i < nodesLength; i++) {
                    for(var j = 0; j < classesLength; j++)  {
                        if(nodes[i].className.search("\\b" + classes[j] + "\\b") != -1) {
                            result.push(nodes[i]);
                            break;
                        }
                    }
                }
                 return result;
            }
        }

    }

    //NOTICE: Mootools present in Joomla version v1.5.x and newer
    window.addEvent('domready', function() {
        var mToolTip = new magicToolTip('magictooltip');
        mToolTip.initialize();
    });
