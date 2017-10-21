(function(C) {
	C.ui = {
		plugin: {
			add: function(E, D, H) {
				var G = C.ui[E].prototype;
				for (var F in H) {
					G.plugins[F] = G.plugins[F] || [];
					G.plugins[F].push([D, H[F]])
				}
			},
			call: function(D, F, E) {
				var H = D.plugins[F];
				if (!H) {
					return
				}
				for (var G = 0; G < H.length; G++) {
					if (D.options[H[G][0]]) {
						H[G][1].apply(D.element, E)
					}
				}
			}
		},
		cssCache: {},
		css: function(D) {
			if (C.ui.cssCache[D]) {
				return C.ui.cssCache[D]
			}
			var E = C('<div class="ui-gen">').addClass(D).css({
				position: "absolute",
				top: "-5000px",
				left: "-5000px",
				display: "block"
			}).appendTo("body");
			C.ui.cssCache[D] = !!((!(/auto|default/).test(E.css("cursor")) || (/^[1-9]/).test(E.css("height")) || (/^[1-9]/).test(E.css("width")) || !(/none/).test(E.css("backgroundImage")) || !(/transparent|rgba\(0, 0, 0, 0\)/).test(E.css("backgroundColor"))));
			try {
				C("body").get(0).removeChild(E.get(0))
			} catch(F) {}
			return C.ui.cssCache[D]
		},
		disableSelection: function(D) {
			C(D).attr("unselectable", "on").css("MozUserSelect", "none")
		},
		enableSelection: function(D) {
			C(D).attr("unselectable", "off").css("MozUserSelect", "")
		},
		hasScroll: function(G, D) {
			var F = /top/.test(D || "top") ? "scrollTop": "scrollLeft",
			E = false;
			if (G[F] > 0) {
				return true
			}
			G[F] = 1;
			E = G[F] > 0 ? true: false;
			G[F] = 0;
			return E
		}
	};
	var A = C.fn.remove;
	C.fn.remove = function() {
		C("*", this).add(this).triggerHandler("remove");
		return A.apply(this, arguments)
	};
	function B(F, D, G) {
		var E = C[F][D].getter || [];
		E = (typeof E == "string" ? E.split(/,?\s+/) : E);
		return (C.inArray(G, E) != -1)
	}
	C.widget = function(D, E) {
		var F = D.split(".")[0];
		D = D.split(".")[1];
		C.fn[D] = function(J) {
			var H = (typeof J == "string"),
			I = Array.prototype.slice.call(arguments, 1);
			if (H && B(F, D, J)) {
				var G = C.data(this[0], D);
				return (G ? G[J].apply(G, I) : undefined)
			}
			return this.each(function() {
				var K = C.data(this, D);
				if (H && K && C.isFunction(K[J])) {
					K[J].apply(K, I)
				} else {
					if (!H) {
						C.data(this, D, new C[F][D](this, J))
					}
				}
			})
		};
		C[F][D] = function(H, I) {
			var G = this;
			this.widgetName = D;
			this.widgetBaseClass = F + "-" + D;
			this.options = C.extend({},
			C.widget.defaults, C[F][D].defaults, I);
			this.element = C(H).bind("setData." + D,
			function(L, J, K) {
				return G.setData(J, K)
			}).bind("getData." + D,
			function(K, J) {
				return G.getData(J)
			}).bind("remove",
			function() {
				return G.destroy()
			});
			this.init()
		};
		C[F][D].prototype = C.extend({},
		C.widget.prototype, E)
	};
	C.widget.prototype = {
		init: function() {},
		destroy: function() {
			this.element.removeData(this.widgetName)
		},
		getData: function(D) {
			return this.options[D]
		},
		setData: function(D, E) {
			this.options[D] = E;
			if (D == "disabled") {
				this.element[E ? "addClass": "removeClass"](this.widgetBaseClass + "-disabled")
			}
		},
		enable: function() {
			this.setData("disabled", false)
		},
		disable: function() {
			this.setData("disabled", true)
		}
	};
	C.widget.defaults = {
		disabled: false
	};
	C.ui.mouse = {
		mouseInit: function() {
			var D = this;
			this.element.bind("mousedown." + this.widgetName,
			function(E) {
				return D.mouseDown(E)
			});
			if (C.browser.msie) {
				this._mouseUnselectable = this.element.attr("unselectable");
				this.element.attr("unselectable", "on")
			}
			this.started = false
		},
		mouseDestroy: function() {
			this.element.unbind("." + this.widgetName); (C.browser.msie && this.element.attr("unselectable", this._mouseUnselectable))
		},
		mouseDown: function(F) { (this._mouseStarted && this.mouseUp(F));
			this._mouseDownEvent = F;
			var D = this,
			G = (F.which == 1),
			E = (typeof this.options.cancel == "string" ? C(F.target).parents().add(F.target).filter(this.options.cancel).length: false);
			if (!G || E || !this.mouseCapture(F)) {
				return true
			}
			this._mouseDelayMet = !this.options.delay;
			if (!this._mouseDelayMet) {
				this._mouseDelayTimer = setTimeout(function() {
					D._mouseDelayMet = true
				},
				this.options.delay)
			}
			if (this.mouseDistanceMet(F) && this.mouseDelayMet(F)) {
				this._mouseStarted = (this.mouseStart(F) !== false);
				if (!this._mouseStarted) {
					F.preventDefault();
					return true
				}
			}
			this._mouseMoveDelegate = function(H) {
				return D.mouseMove(H)
			};
			this._mouseUpDelegate = function(H) {
				return D.mouseUp(H)
			};
			C(document).bind("mousemove." + this.widgetName, this._mouseMoveDelegate).bind("mouseup." + this.widgetName, this._mouseUpDelegate);
			return false
		},
		mouseMove: function(D) {
			if (C.browser.msie && !D.button) {
				return this.mouseUp(D)
			}
			if (this._mouseStarted) {
				this.mouseDrag(D);
				return false
			}
			if (this.mouseDistanceMet(D) && this.mouseDelayMet(D)) {
				this._mouseStarted = (this.mouseStart(this._mouseDownEvent, D) !== false); (this._mouseStarted ? this.mouseDrag(D) : this.mouseUp(D))
			}
			return ! this._mouseStarted
		},
		mouseUp: function(D) {
			C(document).unbind("mousemove." + this.widgetName, this._mouseMoveDelegate).unbind("mouseup." + this.widgetName, this._mouseUpDelegate);
			if (this._mouseStarted) {
				this._mouseStarted = false;
				this.mouseStop(D)
			}
			return false
		},
		mouseDistanceMet: function(D) {
			return (Math.max(Math.abs(this._mouseDownEvent.pageX - D.pageX), Math.abs(this._mouseDownEvent.pageY - D.pageY)) >= this.options.distance)
		},
		mouseDelayMet: function(D) {
			return this._mouseDelayMet
		},
		mouseStart: function(D) {},
		mouseDrag: function(D) {},
		mouseStop: function(D) {},
		mouseCapture: function(D) {
			return true
		}
	};
	C.ui.mouse.defaults = {
		cancel: null,
		distance: 1,
		delay: 0
	}
})(jQuery); (function(A) {
	A.widget("ui.draggable", A.extend({},
	A.ui.mouse, {
		init: function() {
			var B = this.options;
			if (B.helper == "original" && !(/(relative|absolute|fixed)/).test(this.element.css("position"))) {
				this.element.css("position", "relative")
			}
			this.element.addClass("ui-draggable"); (B.disabled && this.element.addClass("ui-draggable-disabled"));
			this.mouseInit()
		},
		mouseStart: function(F) {
			var H = this.options;
			if (this.helper || H.disabled || A(F.target).is(".ui-resizable-handle")) {
				return false
			}
			var B = !this.options.handle || !A(this.options.handle, this.element).length ? true: false;
			A(this.options.handle, this.element).find("*").andSelf().each(function() {
				if (this == F.target) {
					B = true
				}
			});
			if (!B) {
				return false
			}
			if (A.ui.ddmanager) {
				A.ui.ddmanager.current = this
			}
			this.helper = A.isFunction(H.helper) ? A(H.helper.apply(this.element[0], [F])) : (H.helper == "clone" ? this.element.clone() : this.element);
			if (!this.helper.parents("body").length) {
				this.helper.appendTo((H.appendTo == "parent" ? this.element[0].parentNode: H.appendTo))
			}
			if (this.helper[0] != this.element[0] && !(/(fixed|absolute)/).test(this.helper.css("position"))) {
				this.helper.css("position", "absolute")
			}
			this.margins = {
				left: (parseInt(this.element.css("marginLeft"), 10) || 0),
				top: (parseInt(this.element.css("marginTop"), 10) || 0)
			};
			this.cssPosition = this.helper.css("position");
			this.offset = this.element.offset();
			this.offset = {
				top: this.offset.top - this.margins.top,
				left: this.offset.left - this.margins.left
			};
			this.offset.click = {
				left: F.pageX - this.offset.left,
				top: F.pageY - this.offset.top
			};
			this.offsetParent = this.helper.offsetParent();
			var C = this.offsetParent.offset();
			if (this.offsetParent[0] == document.body && A.browser.mozilla) {
				C = {
					top: 0,
					left: 0
				}
			}
			this.offset.parent = {
				top: C.top + (parseInt(this.offsetParent.css("borderTopWidth"), 10) || 0),
				left: C.left + (parseInt(this.offsetParent.css("borderLeftWidth"), 10) || 0)
			};
			var E = this.element.position();
			this.offset.relative = this.cssPosition == "relative" ? {
				top: E.top - (parseInt(this.helper.css("top"), 10) || 0) + this.offsetParent[0].scrollTop,
				left: E.left - (parseInt(this.helper.css("left"), 10) || 0) + this.offsetParent[0].scrollLeft
			}: {
				top: 0,
				left: 0
			};
			this.originalPosition = this.generatePosition(F);
			this.helperProportions = {
				width: this.helper.outerWidth(),
				height: this.helper.outerHeight()
			};
			if (H.cursorAt) {
				if (H.cursorAt.left != undefined) {
					this.offset.click.left = H.cursorAt.left + this.margins.left
				}
				if (H.cursorAt.right != undefined) {
					this.offset.click.left = this.helperProportions.width - H.cursorAt.right + this.margins.left
				}
				if (H.cursorAt.top != undefined) {
					this.offset.click.top = H.cursorAt.top + this.margins.top
				}
				if (H.cursorAt.bottom != undefined) {
					this.offset.click.top = this.helperProportions.height - H.cursorAt.bottom + this.margins.top
				}
			}
			if (H.containment) {
				if (H.containment == "parent") {
					H.containment = this.helper[0].parentNode
				}
				if (H.containment == "document" || H.containment == "window") {
					this.containment = [0 - this.offset.relative.left - this.offset.parent.left, 0 - this.offset.relative.top - this.offset.parent.top, A(H.containment == "document" ? document: window).width() - this.offset.relative.left - this.offset.parent.left - this.helperProportions.width - this.margins.left - (parseInt(this.element.css("marginRight"), 10) || 0), (A(H.containment == "document" ? document: window).height() || document.body.parentNode.scrollHeight) - this.offset.relative.top - this.offset.parent.top - this.helperProportions.height - this.margins.top - (parseInt(this.element.css("marginBottom"), 10) || 0)]
				}
				if (! (/^(document|window|parent)$/).test(H.containment)) {
					var D = A(H.containment)[0];
					var G = A(H.containment).offset();
					this.containment = [G.left + (parseInt(A(D).css("borderLeftWidth"), 10) || 0) - this.offset.relative.left - this.offset.parent.left, G.top + (parseInt(A(D).css("borderTopWidth"), 10) || 0) - this.offset.relative.top - this.offset.parent.top, G.left + Math.max(D.scrollWidth, D.offsetWidth) - (parseInt(A(D).css("borderLeftWidth"), 10) || 0) - this.offset.relative.left - this.offset.parent.left - this.helperProportions.width - this.margins.left - (parseInt(this.element.css("marginRight"), 10) || 0), G.top + Math.max(D.scrollHeight, D.offsetHeight) - (parseInt(A(D).css("borderTopWidth"), 10) || 0) - this.offset.relative.top - this.offset.parent.top - this.helperProportions.height - this.margins.top - (parseInt(this.element.css("marginBottom"), 10) || 0)]
				}
			}
			this.propagate("start", F);
			this.helperProportions = {
				width: this.helper.outerWidth(),
				height: this.helper.outerHeight()
			};
			if (A.ui.ddmanager && !H.dropBehaviour) {
				A.ui.ddmanager.prepareOffsets(this, F)
			}
			this.helper.addClass("ui-draggable-dragging");
			this.mouseDrag(F);
			return true
		},
		convertPositionTo: function(C, D) {
			if (!D) {
				D = this.position
			}
			var B = C == "absolute" ? 1: -1;
			return {
				top: (D.top + this.offset.relative.top * B + this.offset.parent.top * B - (this.cssPosition == "fixed" || (this.cssPosition == "absolute" && this.offsetParent[0] == document.body) ? 0: this.offsetParent[0].scrollTop) * B + (this.cssPosition == "fixed" ? A(document).scrollTop() : 0) * B + this.margins.top * B),
				left: (D.left + this.offset.relative.left * B + this.offset.parent.left * B - (this.cssPosition == "fixed" || (this.cssPosition == "absolute" && this.offsetParent[0] == document.body) ? 0: this.offsetParent[0].scrollLeft) * B + (this.cssPosition == "fixed" ? A(document).scrollLeft() : 0) * B + this.margins.left * B)
			}
		},
		generatePosition: function(E) {
			var F = this.options;
			var B = {
				top: (E.pageY - this.offset.click.top - this.offset.relative.top - this.offset.parent.top + (this.cssPosition == "fixed" || (this.cssPosition == "absolute") ? 0: this.offsetParent[0].scrollTop) - (this.cssPosition == "fixed" ? A(document).scrollTop() : 0)),
				left: (E.pageX - this.offset.click.left - this.offset.relative.left - this.offset.parent.left + (this.cssPosition == "fixed" || (this.cssPosition == "absolute" && this.offsetParent[0] == document.body) ? 0: this.offsetParent[0].scrollLeft) - (this.cssPosition == "fixed" ? A(document).scrollLeft() : 0))
			};
			if (!this.originalPosition) {
				return B
			}
			if (this.containment) {
				if (B.left < this.containment[0]) {
					B.left = this.containment[0]
				}
				if (B.top < this.containment[1]) {
					B.top = this.containment[1]
				}
				if (B.left > this.containment[2]) {
					B.left = this.containment[2]
				}
				if (B.top > this.containment[3]) {
					B.top = this.containment[3]
				}
			}
			if (F.grid) {
				var D = this.originalPosition.top + Math.round((B.top - this.originalPosition.top) / F.grid[1]) * F.grid[1];
				B.top = this.containment ? (!(D < this.containment[1] || D > this.containment[3]) ? D: (!(D < this.containment[1]) ? D - F.grid[1] : D + F.grid[1])) : D;
				var C = this.originalPosition.left + Math.round((B.left - this.originalPosition.left) / F.grid[0]) * F.grid[0];
				B.left = this.containment ? (!(C < this.containment[0] || C > this.containment[2]) ? C: (!(C < this.containment[0]) ? C - F.grid[0] : C + F.grid[0])) : C
			}
			return B
		},
		mouseDrag: function(B) {
			this.position = this.generatePosition(B);
			this.positionAbs = this.convertPositionTo("absolute");
			this.position = this.propagate("drag", B) || this.position;
			if (!this.options.axis || this.options.axis != "y") {
				this.helper[0].style.left = this.position.left + "px"
			}
			if (!this.options.axis || this.options.axis != "x") {
				this.helper[0].style.top = this.position.top + "px"
			}
			if (A.ui.ddmanager) {
				A.ui.ddmanager.drag(this, B)
			}
			return false
		},
		mouseStop: function(C) {
			var D = false;
			if (A.ui.ddmanager && !this.options.dropBehaviour) {
				var D = A.ui.ddmanager.drop(this, C)
			}
			if ((this.options.revert == "invalid" && !D) || (this.options.revert == "valid" && D) || this.options.revert === true) {
				var B = this;
				A(this.helper).animate(this.originalPosition, parseInt(this.options.revert, 10) || 500,
				function() {
					B.propagate("stop", C);
					B.clear()
				})
			} else {
				this.propagate("stop", C);
				this.clear()
			}
			return false
		},
		clear: function() {
			this.helper.removeClass("ui-draggable-dragging");
			if (this.options.helper != "original" && !this.cancelHelperRemoval) {
				this.helper.remove()
			}
			this.helper = null;
			this.cancelHelperRemoval = false
		},
		plugins: {},
		uiHash: function(B) {
			return {
				helper: this.helper,
				position: this.position,
				absolutePosition: this.positionAbs,
				options: this.options
			}
		},
		propagate: function(C, B) {
			A.ui.plugin.call(this, C, [B, this.uiHash()]);
			if (C == "drag") {
				this.positionAbs = this.convertPositionTo("absolute")
			}
			return this.element.triggerHandler(C == "drag" ? C: "drag" + C, [B, this.uiHash()], this.options[C])
		},
		destroy: function() {
			if (!this.element.data("draggable")) {
				return
			}
			this.element.removeData("draggable").unbind(".draggable").removeClass("ui-draggable");
			this.mouseDestroy()
		}
	}));
	A.extend(A.ui.draggable, {
		defaults: {
			appendTo: "parent",
			axis: false,
			cancel: ":input",
			delay: 0,
			distance: 1,
			helper: "original"
		}
	});
	A.ui.plugin.add("draggable", "cursor", {
		start: function(D, C) {
			var B = A("body");
			if (B.css("cursor")) {
				C.options._cursor = B.css("cursor")
			}
			B.css("cursor", C.options.cursor)
		},
		stop: function(C, B) {
			if (B.options._cursor) {
				A("body").css("cursor", B.options._cursor)
			}
		}
	});
	A.ui.plugin.add("draggable", "zIndex", {
		start: function(D, C) {
			var B = A(C.helper);
			if (B.css("zIndex")) {
				C.options._zIndex = B.css("zIndex")
			}
			B.css("zIndex", C.options.zIndex)
		},
		stop: function(C, B) {
			if (B.options._zIndex) {
				A(B.helper).css("zIndex", B.options._zIndex)
			}
		}
	});
	A.ui.plugin.add("draggable", "opacity", {
		start: function(D, C) {
			var B = A(C.helper);
			if (B.css("opacity")) {
				C.options._opacity = B.css("opacity")
			}
			B.css("opacity", C.options.opacity)
		},
		stop: function(C, B) {
			if (B.options._opacity) {
				A(B.helper).css("opacity", B.options._opacity)
			}
		}
	});
	A.ui.plugin.add("draggable", "iframeFix", {
		start: function(C, B) {
			A(B.options.iframeFix === true ? "iframe": B.options.iframeFix).each(function() {
				A('<div class="ui-draggable-iframeFix" style="background: #fff;"></div>').css({
					width: this.offsetWidth + "px",
					height: this.offsetHeight + "px",
					position: "absolute",
					opacity: "0.001",
					zIndex: 1000
				}).css(A(this).offset()).appendTo("body")
			})
		},
		stop: function(C, B) {
			A("div.DragDropIframeFix").each(function() {
				this.parentNode.removeChild(this)
			})
		}
	});
	A.ui.plugin.add("draggable", "scroll", {
		start: function(D, C) {
			var E = C.options;
			var B = A(this).data("draggable");
			E.scrollSensitivity = E.scrollSensitivity || 20;
			E.scrollSpeed = E.scrollSpeed || 20;
			B.overflowY = function(F) {
				do {
					if (/auto|scroll/.test(F.css("overflow")) || (/auto|scroll/).test(F.css("overflow-y"))) {
						return F
					}
					F = F.parent()
				}
				while (F[0].parentNode);
				return A(document)
			} (this);
			B.overflowX = function(F) {
				do {
					if (/auto|scroll/.test(F.css("overflow")) || (/auto|scroll/).test(F.css("overflow-x"))) {
						return F
					}
					F = F.parent()
				}
				while (F[0].parentNode);
				return A(document)
			} (this);
			if (B.overflowY[0] != document && B.overflowY[0].tagName != "HTML") {
				B.overflowYOffset = B.overflowY.offset()
			}
			if (B.overflowX[0] != document && B.overflowX[0].tagName != "HTML") {
				B.overflowXOffset = B.overflowX.offset()
			}
		},
		drag: function(D, C) {
			var E = C.options;
			var B = A(this).data("draggable");
			if (B.overflowY[0] != document && B.overflowY[0].tagName != "HTML") {
				if ((B.overflowYOffset.top + B.overflowY[0].offsetHeight) - D.pageY < E.scrollSensitivity) {
					B.overflowY[0].scrollTop = B.overflowY[0].scrollTop + E.scrollSpeed
				}
				if (D.pageY - B.overflowYOffset.top < E.scrollSensitivity) {
					B.overflowY[0].scrollTop = B.overflowY[0].scrollTop - E.scrollSpeed
				}
			} else {
				if (D.pageY - A(document).scrollTop() < E.scrollSensitivity) {
					A(document).scrollTop(A(document).scrollTop() - E.scrollSpeed)
				}
				if (A(window).height() - (D.pageY - A(document).scrollTop()) < E.scrollSensitivity) {
					A(document).scrollTop(A(document).scrollTop() + E.scrollSpeed)
				}
			}
			if (B.overflowX[0] != document && B.overflowX[0].tagName != "HTML") {
				if ((B.overflowXOffset.left + B.overflowX[0].offsetWidth) - D.pageX < E.scrollSensitivity) {
					B.overflowX[0].scrollLeft = B.overflowX[0].scrollLeft + E.scrollSpeed
				}
				if (D.pageX - B.overflowXOffset.left < E.scrollSensitivity) {
					B.overflowX[0].scrollLeft = B.overflowX[0].scrollLeft - E.scrollSpeed
				}
			} else {
				if (D.pageX - A(document).scrollLeft() < E.scrollSensitivity) {
					A(document).scrollLeft(A(document).scrollLeft() - E.scrollSpeed)
				}
				if (A(window).width() - (D.pageX - A(document).scrollLeft()) < E.scrollSensitivity) {
					A(document).scrollLeft(A(document).scrollLeft() + E.scrollSpeed)
				}
			}
		}
	});
	A.ui.plugin.add("draggable", "snap", {
		start: function(D, C) {
			var B = A(this).data("draggable");
			B.snapElements = [];
			A(C.options.snap === true ? ".ui-draggable": C.options.snap).each(function() {
				var F = A(this);
				var E = F.offset();
				if (this != B.element[0]) {
					B.snapElements.push({
						item: this,
						width: F.outerWidth(),
						height: F.outerHeight(),
						top: E.top,
						left: E.left
					})
				}
			})
		},
		drag: function(J, O) {
			var I = A(this).data("draggable");
			var L = O.options.snapTolerance || 20;
			var D = O.absolutePosition.left,
			C = D + I.helperProportions.width,
			P = O.absolutePosition.top,
			N = P + I.helperProportions.height;
			for (var H = I.snapElements.length - 1; H >= 0; H--) {
				var E = I.snapElements[H].left,
				B = E + I.snapElements[H].width,
				R = I.snapElements[H].top,
				M = R + I.snapElements[H].height;
				if (! ((E - L < D && D < B + L && R - L < P && P < M + L) || (E - L < D && D < B + L && R - L < N && N < M + L) || (E - L < C && C < B + L && R - L < P && P < M + L) || (E - L < C && C < B + L && R - L < N && N < M + L))) {
					continue
				}
				if (O.options.snapMode != "inner") {
					var K = Math.abs(R - N) <= 20;
					var Q = Math.abs(M - P) <= 20;
					var G = Math.abs(E - C) <= 20;
					var F = Math.abs(B - D) <= 20;
					if (K) {
						O.position.top = I.convertPositionTo("relative", {
							top: R - I.helperProportions.height,
							left: 0
						}).top
					}
					if (Q) {
						O.position.top = I.convertPositionTo("relative", {
							top: M,
							left: 0
						}).top
					}
					if (G) {
						O.position.left = I.convertPositionTo("relative", {
							top: 0,
							left: E - I.helperProportions.width
						}).left
					}
					if (F) {
						O.position.left = I.convertPositionTo("relative", {
							top: 0,
							left: B
						}).left
					}
				}
				if (O.options.snapMode != "outer") {
					var K = Math.abs(R - P) <= 20;
					var Q = Math.abs(M - N) <= 20;
					var G = Math.abs(E - D) <= 20;
					var F = Math.abs(B - C) <= 20;
					if (K) {
						O.position.top = I.convertPositionTo("relative", {
							top: R,
							left: 0
						}).top
					}
					if (Q) {
						O.position.top = I.convertPositionTo("relative", {
							top: M - I.helperProportions.height,
							left: 0
						}).top
					}
					if (G) {
						O.position.left = I.convertPositionTo("relative", {
							top: 0,
							left: E
						}).left
					}
					if (F) {
						O.position.left = I.convertPositionTo("relative", {
							top: 0,
							left: B - I.helperProportions.width
						}).left
					}
				}
			}
		}
	});
	A.ui.plugin.add("draggable", "connectToSortable", {
		start: function(D, C) {
			var B = A(this).data("draggable");
			B.sortables = [];
			A(C.options.connectToSortable).each(function() {
				if (A.data(this, "sortable")) {
					var E = A.data(this, "sortable");
					B.sortables.push({
						instance: E,
						shouldRevert: E.options.revert
					});
					E.refreshItems();
					E.propagate("activate", D, B)
				}
			})
		},
		stop: function(D, C) {
			var B = A(this).data("draggable");
			A.each(B.sortables,
			function() {
				if (this.instance.isOver) {
					this.instance.isOver = 0;
					B.cancelHelperRemoval = true;
					this.instance.cancelHelperRemoval = false;
					if (this.shouldRevert) {
						this.instance.options.revert = true
					}
					this.instance.mouseStop(D);
					this.instance.element.triggerHandler("sortreceive", [D, A.extend(this.instance.ui(), {
						sender: B.element
					})], this.instance.options.receive);
					this.instance.options.helper = this.instance.options._helper
				} else {
					this.instance.propagate("deactivate", D, B)
				}
			})
		},
		drag: function(F, E) {
			var D = A(this).data("draggable"),
			B = this;
			var C = function(K) {
				var H = K.left,
				J = H + K.width,
				I = K.top,
				G = I + K.height;
				return (H < (this.positionAbs.left + this.offset.click.left) && (this.positionAbs.left + this.offset.click.left) < J && I < (this.positionAbs.top + this.offset.click.top) && (this.positionAbs.top + this.offset.click.top) < G)
			};
			A.each(D.sortables,
			function(G) {
				if (C.call(D, this.instance.containerCache)) {
					if (!this.instance.isOver) {
						this.instance.isOver = 1;
						this.instance.currentItem = A(B).clone().appendTo(this.instance.element).data("sortable-item", true);
						this.instance.options._helper = this.instance.options.helper;
						this.instance.options.helper = function() {
							return E.helper[0]
						};
						F.target = this.instance.currentItem[0];
						this.instance.mouseCapture(F, true);
						this.instance.mouseStart(F, true, true);
						this.instance.offset.click.top = D.offset.click.top;
						this.instance.offset.click.left = D.offset.click.left;
						this.instance.offset.parent.left -= D.offset.parent.left - this.instance.offset.parent.left;
						this.instance.offset.parent.top -= D.offset.parent.top - this.instance.offset.parent.top;
						D.propagate("toSortable", F)
					}
					if (this.instance.currentItem) {
						this.instance.mouseDrag(F)
					}
				} else {
					if (this.instance.isOver) {
						this.instance.isOver = 0;
						this.instance.cancelHelperRemoval = true;
						this.instance.options.revert = false;
						this.instance.mouseStop(F, true);
						this.instance.options.helper = this.instance.options._helper;
						this.instance.currentItem.remove();
						if (this.instance.placeholder) {
							this.instance.placeholder.remove()
						}
						D.propagate("fromSortable", F)
					}
				}
			})
		}
	});
	A.ui.plugin.add("draggable", "stack", {
		start: function(D, B) {
			var C = A.makeArray(A(B.options.stack.group)).sort(function(F, E) {
				return (parseInt(A(F).css("zIndex"), 10) || B.options.stack.min) - (parseInt(A(E).css("zIndex"), 10) || B.options.stack.min)
			});
			A(C).each(function(E) {
				this.style.zIndex = B.options.stack.min + E
			});
			this[0].style.zIndex = B.options.stack.min + C.length
		}
	})
})(jQuery);