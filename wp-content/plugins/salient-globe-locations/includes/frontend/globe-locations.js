(function ($) {
	'use strict';

	var config = window.sglGlobeLocations || {};
	var mobileBreakpoint = config.mobileBreakpoint || 690;

	function isMobile() {
		return window.matchMedia('(max-width: ' + mobileBreakpoint + 'px)').matches;
	}

	function GlobeLocations($root) {
		this.$root = $root;
		this.selectedIndex = null;
		this.hoverIndex = null;
		this.instanceId = this.$root.attr('id') || 'sgl-default';
		this.$liveRegion = $('#' + this.instanceId + '-live');
		this.$cardsArea = this.$root.find('.sgl-cards');
		this.$scroll = this.$root.find('.sgl-cards__scroll');
		this.$track = this.$root.find('.sgl-cards__track');
		this.$primaryGroup = this.$root.find('.sgl-cards__group:not([aria-hidden="true"])').first();
		this.trackOffsetX = 0;
		this.pointerActive = false;
		this.activePointerId = null;
		this.isDragging = false;
		this.dragStartX = 0;
		this.dragStartOffset = 0;
		this.dragMoved = false;
		this.hasManualPosition = false;
		this.ignoreMouseLeaveExit = false;
		this.ignoreNextCardClick = false;
		this.init();
	}

	GlobeLocations.prototype.init = function () {
		var self = this;

		if (this.$root.data('sgl-initialized')) {
			return;
		}

		this.$root.data('sgl-initialized', true);
		this.$pins = this.$root.find('.sgl-pin');
		this.$cardButtons = this.$root.find('.sgl-cards__group:not([aria-hidden="true"]) .sgl-card__select');
		this.$cards = this.$root.find('.sgl-card');

		var trackDuration = parseFloat(window.getComputedStyle(this.$track[0]).animationDuration);

		if (!isNaN(trackDuration) && trackDuration > 0) {
			this.scrollAnimationDuration = trackDuration;
		} else {
			this.scrollAnimationDuration = 40;
		}

		this.bindInteractionEvents();
		this.bindDragEvents();

		$(window).on('resize.sglGlobe', function () {
			self.handleResize();
		});
	};

	GlobeLocations.prototype.handleResize = function () {
		if (isMobile()) {
			this.clearHover();
			this.hasManualPosition = false;
			this.exitManualScroll(true);
		}
	};

	GlobeLocations.prototype.parseIndex = function (index) {
		index = parseInt(index, 10);
		return isNaN(index) ? null : index;
	};

	GlobeLocations.prototype.getDisplayIndex = function () {
		if (this.hoverIndex !== null) {
			return this.hoverIndex;
		}

		return this.selectedIndex;
	};

	GlobeLocations.prototype.getLocationData = function ($el) {
		return {
			name: $el.attr('data-name') || '',
			address: $el.attr('data-address') || '',
			phone: $el.attr('data-phone') || '',
		};
	};

	GlobeLocations.prototype.buildAnnouncement = function (data) {
		var parts = [];
		var selectedLabel = config.selectedLabel || 'selected';
		var phoneLabel = config.phoneLabel || 'Phone:';

		if (data.name) {
			parts.push(data.name + ' ' + selectedLabel);
		}

		if (data.address) {
			parts.push(data.address);
		}

		if (data.phone) {
			parts.push(phoneLabel + ' ' + data.phone);
		}

		return parts.join('. ');
	};

	GlobeLocations.prototype.announce = function (message) {
		if (!this.$liveRegion.length || !message) {
			return;
		}

		this.$liveRegion.text('');
		this.$liveRegion.text(message);
	};

	GlobeLocations.prototype.applyActiveState = function () {
		var displayIndex = this.getDisplayIndex();

		this.$pins.attr('aria-pressed', 'false');
		this.$cardButtons.attr('aria-pressed', 'false');
		this.$pins.removeClass('is-active is-selected');
		this.$cards.removeClass('is-active is-selected');

		if (displayIndex === null) {
			this.$root.removeClass('is-interacting has-selection');
			return;
		}

		this.$root.addClass('is-interacting');

		if (this.selectedIndex !== null) {
			this.$root.addClass('has-selection');
		} else {
			this.$root.removeClass('has-selection');
		}
		this.$pins.filter('[data-index="' + displayIndex + '"]').addClass('is-active');
		this.$cards.filter('[data-index="' + displayIndex + '"]').addClass('is-active');

		if (this.selectedIndex !== null) {
			this.$pins.filter('[data-index="' + this.selectedIndex + '"]').addClass('is-selected').attr('aria-pressed', 'true');
			this.$cardButtons.filter('[data-index="' + this.selectedIndex + '"]').attr('aria-pressed', 'true');
			this.$cards.filter('[data-index="' + this.selectedIndex + '"]').addClass('is-selected');
		}
	};

	GlobeLocations.prototype.selectIndex = function (index, announce) {
		index = this.parseIndex(index);

		if (index === null) {
			return;
		}

		this.selectedIndex = index;
		this.applyActiveState();

		if (announce !== false) {
			var $el = this.$cardButtons.filter('[data-index="' + index + '"]').first();

			if (!$el.length) {
				$el = this.$pins.filter('[data-index="' + index + '"]').first();
			}

			this.announce(this.buildAnnouncement(this.getLocationData($el)));
		}
	};

	GlobeLocations.prototype.setHoverIndex = function (index) {
		if (isMobile()) {
			return;
		}

		index = this.parseIndex(index);

		if (index === null) {
			return;
		}

		this.hoverIndex = index;
		this.applyActiveState();
	};

	GlobeLocations.prototype.clearHover = function () {
		this.hoverIndex = null;
		this.applyActiveState();
	};

	GlobeLocations.prototype.clearSelection = function (announce) {
		this.selectedIndex = null;
		this.hoverIndex = null;
		this.applyActiveState();

		if (announce !== false) {
			this.announce(config.clearedLabel || 'Selection cleared');
		}
	};

	GlobeLocations.prototype.isLocationInteractionTarget = function (target) {
		return $(target).closest('.sgl-pin, .sgl-card__select, .sgl-cards').length > 0;
	};

	GlobeLocations.prototype.consumeIgnoreNextClick = function () {
		if (!this.ignoreNextCardClick) {
			return false;
		}

		this.ignoreNextCardClick = false;
		return true;
	};

	GlobeLocations.prototype.syncTrackOffsetFromLayout = function () {
		var scrollRect = this.$scroll[0].getBoundingClientRect();
		var trackRect = this.$track[0].getBoundingClientRect();

		return trackRect.left - scrollRect.left;
	};

	GlobeLocations.prototype.getGroupWidth = function () {
		if (!this.$primaryGroup.length) {
			return 0;
		}

		return this.$primaryGroup[0].getBoundingClientRect().width;
	};

	GlobeLocations.prototype.setTrackOffset = function (x) {
		var groupWidth = this.getGroupWidth();

		if (groupWidth > 0) {
			while (x < -groupWidth) {
				x += groupWidth;
			}

			while (x > 0) {
				x -= groupWidth;
			}
		}

		this.trackOffsetX = x;
		this.$track[0].style.transform = 'translateX(' + x + 'px)';
	};

	GlobeLocations.prototype.enterManualScroll = function () {
		if (isMobile()) {
			return;
		}

		if (!this.$track.hasClass('is-manual')) {
			this.trackOffsetX = this.syncTrackOffsetFromLayout();
			this.$track[0].style.animationDelay = '';
			this.$track[0].style.transform = 'translateX(' + this.trackOffsetX + 'px)';
			this.$track.addClass('is-manual');
			return;
		}

		this.$track[0].style.transform = 'translateX(' + this.trackOffsetX + 'px)';
	};

	GlobeLocations.prototype.exitManualScroll = function (force) {
		if (isMobile()) {
			return;
		}

		if (!force && (this.isDragging || this.pointerActive || this.selectedIndex !== null)) {
			return;
		}

		if (!this.$track.hasClass('is-manual')) {
			return;
		}

		var groupWidth = this.getGroupWidth();
		var offset = this.trackOffsetX;

		this.$track.removeClass('is-manual');
		this.$track[0].style.transform = '';
		this.trackOffsetX = 0;

		if (groupWidth > 0 && offset !== 0) {
			var progress = Math.abs(offset) / groupWidth;

			progress = progress - Math.floor(progress);

			this.$track[0].style.animationDelay = '-' + (progress * this.scrollAnimationDuration) + 's';
		} else {
			this.$track[0].style.animationDelay = '';
		}
	};

	GlobeLocations.prototype.snapToIndex = function (index) {
		if (isMobile()) {
			return;
		}

		index = this.parseIndex(index);

		if (index === null) {
			return;
		}

		var $card = this.$root.find('.sgl-cards__group:not([aria-hidden="true"]) .sgl-card[data-index="' + index + '"]').first();

		if (!$card.length) {
			return;
		}

		this.enterManualScroll();

		var scrollRect = this.$scroll[0].getBoundingClientRect();
		var cardRect = $card[0].getBoundingClientRect();
		var scrollCenter = scrollRect.left + scrollRect.width / 2;
		var cardCenter = cardRect.left + cardRect.width / 2;

		this.setTrackOffset(this.trackOffsetX + (scrollCenter - cardCenter));
	};

	GlobeLocations.prototype.slideToIndex = function (index) {
		this.snapToIndex(index);
	};

	GlobeLocations.prototype.focusSibling = function ($items, $current, direction) {
		var index = $items.index($current);

		if (index === -1) {
			return;
		}

		var nextIndex = index + direction;

		if (nextIndex < 0) {
			nextIndex = $items.length - 1;
		} else if (nextIndex >= $items.length) {
			nextIndex = 0;
		}

		$items.eq(nextIndex).focus();
	};

	GlobeLocations.prototype.handleKeydown = function (e) {
		var $target = $(e.target);
		var key = e.key;

		if (key === 'Escape') {
			if (this.selectedIndex === null && this.hoverIndex !== null) {
				e.preventDefault();
				this.clearHover();
			}
			return;
		}

		if ($target.hasClass('sgl-card__select')) {
			if (key === 'ArrowLeft' || key === 'ArrowRight') {
				e.preventDefault();
				this.focusSibling(this.$cardButtons, $target, key === 'ArrowRight' ? 1 : -1);
			}
			return;
		}

		if ($target.hasClass('sgl-pin')) {
			if (key === 'ArrowUp' || key === 'ArrowDown') {
				e.preventDefault();
				var $sortedPins = this.$pins.sort(function (a, b) {
					return parseInt($(a).data('index'), 10) - parseInt($(b).data('index'), 10);
				});
				this.focusSibling($sortedPins, $target, key === 'ArrowDown' ? 1 : -1);
			}
		}
	};

	GlobeLocations.prototype.bindInteractionEvents = function () {
		var self = this;
		var docNamespace = 'click.sglGlobe' + this.instanceId;
		var keyNamespace = 'keydown.sglGlobe' + this.instanceId;

		this.$cardsArea.on('mouseenter', '.sgl-card__select', function () {
			if (self.isDragging) {
				return;
			}

			self.setHoverIndex($(this).data('index'));
		});

		this.$cardsArea.on('mouseleave', '.sgl-card__select', function () {
			if (!isMobile() && !self.isDragging) {
				self.clearHover();
			}
		});

		this.$cardButtons.on('focus', function () {
			if (self.isDragging) {
				return;
			}

			self.setHoverIndex($(this).data('index'));
		});

		this.$cardButtons.on('focusout', function (e) {
			if (!isMobile() && !self.$root[0].contains(e.relatedTarget)) {
				self.clearHover();
			}
		});

		this.$cardsArea.on('click', '.sgl-card__select', function (e) {
			if (self.consumeIgnoreNextClick()) {
				return;
			}

			if ($(e.target).closest('.sgl-card__phone a').length) {
				return;
			}

			e.stopPropagation();
			self.selectIndex($(this).data('index'));
		});

		this.$root.on('click', '.sgl-card__phone a', function (e) {
			e.stopPropagation();
		});

		this.$pins.on('click', function (e) {
			e.stopPropagation();
			var index = $(this).data('index');
			self.selectIndex(index);
			self.slideToIndex(index);
		});

		this.$root.on('keydown', function (e) {
			self.handleKeydown(e);
		});

		$(document).on(docNamespace, function (e) {
			if (self.consumeIgnoreNextClick()) {
				return;
			}

			if (self.isLocationInteractionTarget(e.target)) {
				return;
			}

			if (self.selectedIndex !== null) {
				self.clearSelection(false);
			} else if (self.hoverIndex !== null) {
				self.clearHover();
			}
		});

		$(document).on(keyNamespace, function (e) {
			if (e.key !== 'Escape' || self.selectedIndex === null) {
				return;
			}

			e.preventDefault();
			self.clearSelection();
		});
	};

	GlobeLocations.prototype.bindDragEvents = function () {
		var self = this;
		var dragThreshold = 5;
		var pointerNamespace = '.sglDrag' + this.instanceId;

		this.endPointerInteraction = function (e) {
			if (!self.pointerActive && !self.isDragging) {
				return;
			}

			if (e && self.activePointerId !== null && e.pointerId !== self.activePointerId) {
				return;
			}

			var hadDrag = self.dragMoved;

			if (self.isDragging && self.$scroll[0].releasePointerCapture) {
				try {
					self.$scroll[0].releasePointerCapture(self.activePointerId);
				} catch (err) {
					// Pointer may already be released.
				}
			}

			self.pointerActive = false;
			self.isDragging = false;
			self.activePointerId = null;
			self.$scroll.removeClass('is-dragging');
			$(document).off(pointerNamespace);

			if (hadDrag) {
				self.hasManualPosition = true;
				self.setTrackOffset(self.trackOffsetX);
				self.ignoreNextCardClick = true;
				self.ignoreMouseLeaveExit = true;

				window.requestAnimationFrame(function () {
					self.ignoreMouseLeaveExit = false;
				});
			}
		};

		this.$scroll.on('pointerdown', function (e) {
			if (isMobile() || e.button !== 0) {
				return;
			}

			if ($(e.target).closest('.sgl-card__phone a').length) {
				return;
			}

			self.pointerActive = true;
			self.isDragging = false;
			self.dragMoved = false;
			self.dragStartX = e.clientX;
			self.activePointerId = e.pointerId;

			$(document).off(pointerNamespace);

			$(document).on('pointermove' + pointerNamespace, function (moveEvent) {
				if (moveEvent.pointerId !== self.activePointerId || isMobile()) {
					return;
				}

				if (!self.pointerActive && !self.isDragging) {
					return;
				}

				var deltaX = moveEvent.clientX - self.dragStartX;

				if (!self.isDragging && Math.abs(deltaX) > dragThreshold) {
					self.isDragging = true;
					self.dragMoved = true;
					self.enterManualScroll();
					self.dragStartOffset = self.trackOffsetX;
					self.$scroll.addClass('is-dragging');
					self.clearHover();

					if (self.$scroll[0].setPointerCapture) {
						self.$scroll[0].setPointerCapture(self.activePointerId);
					}
				}

				if (self.isDragging) {
					self.setTrackOffset(self.dragStartOffset + deltaX);
				}
			});

			$(document).on('pointerup' + pointerNamespace + ' pointercancel' + pointerNamespace, function (upEvent) {
				self.endPointerInteraction(upEvent);
			});
		});

		this.$scroll.on('click', function (e) {
			if (self.consumeIgnoreNextClick()) {
				e.preventDefault();
				e.stopPropagation();
			}
		});

		this.$cardsArea.on('mouseleave', function () {
			if (self.ignoreMouseLeaveExit || isMobile() || self.isDragging || self.pointerActive || self.selectedIndex !== null) {
				return;
			}

			if (!self.$track.hasClass('is-manual')) {
				return;
			}

			self.hasManualPosition = false;
			self.exitManualScroll();
		});
	};

	function initGlobeLocations() {
		$('.sgl-globe-locations').each(function () {
			new GlobeLocations($(this));
		});
	}

	$(initGlobeLocations);

})(jQuery);
