// Custom page actions
;(function($, M){
  'use strict';
    
  window.sandboxTheme = function(){
    var resize = function resize(){
          var feature,
              featureName;

          /**
           * Resize all features
           */
          for ( featureName in sandboxTheme.features ) {
            if ( sandboxTheme.info['feature-' + featureName] ) {
              feature = sandboxTheme.features[featureName];

              feature.resize();
            }
          }
        },
        ready = function ready(){
          var feature,
              featureName;

          /**
           * Ready all features
           */
          for ( featureName in sandboxTheme.features ) {
            if ( sandboxTheme.info['feature-' + featureName] ) {
              feature = sandboxTheme.features[featureName];

              feature.ready();
            }
          }
        },
        setup = function setup(){
          var featureName,
              feature,
              featureOptions,
              dataName,
              dataValue,
              data = arguments[0],
              is_drupal = data.theme.info.hasOwnProperty('is_drupal') && data.theme.info.is_drupal;
              
          sandboxTheme.info = {};
          sandboxTheme.page = {};
          
          if ( $.isPlainObject(data) ) {
          
            $.log(is_drupal);
            if ( is_drupal ) {
              sandboxTheme.info = data.theme.info;
              sandboxTheme.page = data.page;
            } else {
            
              for ( dataName in data ) {
                dataValue = dataName === 'id' ? parseInt(data[dataName]) : data[dataName];
                                
                if ( dataName !== 'info') {
                  sandboxTheme.page[dataName] = dataValue;
                }
              }
              if ( data.hasOwnProperty('info') ) {
                for ( dataName in data.info ) {
                  sandboxTheme.info[dataName] = data.info[dataName];
                }
              }
            }
          }
          
          $.log(sandboxTheme);
          
          /**
           * Loop through all features to set all neccessary data
           * ====================================================
           * After all their .ready() function will be executet and the nxr.info object gets updated.
           */
          for ( featureName in sandboxTheme.features ) {
            feature = sandboxTheme.features[featureName];
          
            /**
             * Make a copy of merged page feature options and feature options
             * jQuery.extend() will take care for a boolean value of options.page.features[featureName]
             */
            featureOptions = $.extend(true, {}, feature.options || {});

            /**
             * Merge the feature with common feature functions, options and info
             */
            $.extend(true, feature, {
              options: featureOptions,
              info: {
                name: featureName
              }
            });
            sandboxTheme.info['feature-' + featureName] = Boolean(feature.setup());
          }
          
          /**
           * Execute ready event
           */
          $(document).on('ready', function(){
            ready();
          });
    
          /**
           * Execute resize event
           */
          $(window).on('debouncedresize', function() {                
            resize();
          });
        };
    
    return {
      init: function init(){
        setup(arguments[0] || null);
      },
      is_page: function is_page(){
        var id = typeof arguments[0] !== 'undefined' ? parseInt(arguments[0]) : null;
        
        if ( id ) {
          return id === sandboxTheme.page.id;
        } else {
          return sandboxTheme.info.is_page;
        }
      }
    };
  }();
  
  sandboxTheme.features = {
  
    /**
     * equal heights for columns
     * =========================
     */
    equalHeights: {
      options: {
        disableOn: 600,
        selectors: {
          containers: '.articles',
          column: 'article > .inner'
        }
      },
      info: {},
      get_highest: function get_highest(container){
        var selectors = this.options.selectors,
            highest = 0;
        
        $(container).find(selectors.column).each(function(){
          var $column = $(this),
              columnHeight = parseInt($column.innerHeight());
          if ( columnHeight > highest ) {
            highest = columnHeight;
          }
        });
        
        return highest;
      },
      set_heights: function set_heights(container, height){  
        var selectors = this.options.selectors;
          
        $(container).find(selectors.column).each(function(){
          var $column = $(this),
              hasPadding = parseInt($column.css('padding-top')) > 0,
              finalHeight = height;
              
          if ( hasPadding ) {
            finalHeight = height - ( parseInt($column.css('padding-top')) + parseInt($column.css('padding-bottom')) );
          }
          $column.height(finalHeight);
        });
      },
      remove_heights: function remove_heights(container){
        var selectors = this.options.selectors;
        
        $(container).find(selectors.column).each(function(){
          $(this).removeAttr('style');
        });
      },
      equal_heights: function equal_heights(){
        var selectors = this.options.selectors,
            $columns = this.$containers.find(selectors.column),
            equalHeights = this;
            
        this.$containers.each(function(){   
          var $container = this;
          
          setTimeout(function(){
            var highest;
          
            equalHeights.remove_heights($container);
            highest = equalHeights.get_highest($container);
            equalHeights.set_heights($container, highest);
          }, 50);
        });
      },
      resize: function resize(){
        var disable = M.check_breakpoint(this.options.disableOn, true),
            equalHeights = this;
        
        if ( disable ) {
          this.$containers.each(function(){
            equalHeights.remove_heights(this);
          });
        } else {
          this.equal_heights();
        }
      },
      ready: function ready(){        
        this.$containers = $(this.options.selectors.containers);
        this.resize();
      },
      setup: function setup(){
        var isSetup = sandboxTheme.info.is_front_page || sandboxTheme.info.is_home || sandboxTheme.is_page(18) || sandboxTheme.is_page(11);
        return isSetup;
      }
    },
  
    /**
     * Mobile burger menu
     * ==================
     */
    menu: {
      options: {
        disableOn: 600,
        selectors: {
          nav: '#menuBar',
          menu: '#menu-main',
          toggler: '#menu-toggle',
        },
        classNames: {
          togglerClass: 'toggle',
          activeClass: 'active'
        },
        texts: {
          toggleText: 'Toggle Menu'
        }
      },
      info: {},
      enable: function () {
        var options = this.options,
            currentScrollTop = $('html').scrollTop(),
            oldScrollTop = 0;

        this.$menu = $(options.selectors.menu);
        this.$has_subMenu = this.$menu.find('.menu-item-has-children');
        this.$toggler = $('<a href="' + options.selectors.menu + '" id="' + options.selectors.toggler.replace('#', '') + '" class="' + options.classNames.togglerClass + '">' + options.texts.toggleText + '</a>');

        this.$toggler.on({
          click: function(event){
            var $toggler = $(this),
                $header = $toggler.parents('header').eq(0),
                $target = $($toggler.attr('href'));

            if ( $target.is('.' + options.classNames.activeClass) ) {
              $toggler.removeClass(options.classNames.activeClass);
              $target.removeClass(options.classNames.activeClass).next().trigger('focus');
              $header.removeClass('menu-' + options.classNames.activeClass);

              if ( currentScrollTop !== oldScrollTop ) {
                $('html, body').animate({scrollTop:oldScrollTop}, '300', function(){
                  currentScrollTop = oldScrollTop;
                });
              }

            } else {
              $toggler.addClass(options.classNames.activeClass);
              $target.addClass(options.classNames.activeClass).find('a').eq(0).trigger('focus');
              oldScrollTop = $('html').scrollTop();;
              currentScrollTop = 0
              $('html, body').animate({scrollTop:currentScrollTop}, '300', function(){
                $header.addClass('menu-' + options.classNames.activeClass);
              });
            }
            event.preventDefault();
          }
        });

        this.$menu.before(this.$toggler);

        this.$subMenus = this.$has_subMenu.each(function(){ return $(this).children('ul')});

        this.$has_subMenu.children('ul').each(function(){
          var $subMenu = $(this),
              toggle_subMenu = function toggle_subMenu($newActiveSubMenuParent){
                $newActiveSubMenuParent.toggleClass('toggled-' + options.classNames.activeClass);
                $newActiveSubMenuParent.children('ul').slideToggle();
              };

          $subMenu.hide().prepend($('<li class="sub-menu-parent-link" />').prepend($subMenu.prev('a').clone())).prev('a').on({
            click: function click(event){
              var $newActiveSubMenuParent = $(this).parent('li'),
                  $activeSubMenuParent = $newActiveSubMenuParent.siblings('.toggled-' + options.classNames.activeClass);

              if ( $activeSubMenuParent.length > 0 ) {
                toggle_subMenu($activeSubMenuParent)
              }
              toggle_subMenu($newActiveSubMenuParent);

              event.preventDefault();
            }
          });
        });
        
        this.info.isActive = true;

      },
      disable: function () {
        var options = this.options;

        this.$toggler.off('click').remove();
        this.$menu.find('a').off('click');
        this.$menu.find('.sub-menu-parent-link').remove();
        this.$menu.removeAttr('style').removeClass('active');
        this.$menu.find('ul').removeAttr('style');
        this.info.isActive = false;
      },
      resize: function resize(){
        var disable = M.touch ? false : M.check_breakpoint(this.options.disableOn);

        if ( disable ) {
          if ( this.info.isActive ) {
            this.disable();
          }
        } else {
          if ( !this.info.isActive ) {
            this.enable();
          }
        }
      },
      ready: function ready(){
        this.resize();
      },
      setup: function setup(){
        return true;
      }
    },
  
    /**
     * Nicer comments form
     * ===================
     */
    comments: {
      options: {
        selectors: {
          commments: '#comments',
          respond: '#respond',
          commentForm: '#commentform',
          trigger: '.comment-reply-title'
        }
      },
      info: {
        isTriggered: false
      },
      events: function events(){
        var $trigger = this.$trigger,
            $commentForm = this.$commentForm,
            $respond = this.$respond,
            info = this.info;
        
        $trigger.on('click.' + info.name, function(event){
          if ( !info.isTriggered ) {
            $commentForm.trigger('focusin.' + info.name).slideDown(600, 'swing', function(){
              $('html, body').animate({'scrollTop' : $commentForm.offset().top}, 600, 'swing', function(){
                window.location.hash = $commentForm.attr('id');
              });
            });
            info.isTriggered = true;
          } else {
            $commentForm.trigger('blur.' + info.name).slideUp(600, 'swing', function(){
              $('html, body').animate({'scrollTop' : $respond.offset().top}, 600, 'swing', function(){
                window.location.hash = $respond.attr('id');
              });
            });
            info.isTriggered = false;
          }
          event.preventDefault();          
        });
      },
      resize: function resize(){},
      ready: function ready(){
        var selectors = this.options.selectors;
        
        // this.$comments = $(selectors.comments);
        this.$respond = $(selectors.respond);
        this.$commentForm = $(selectors.commentForm).hide();
        this.$trigger = this.$respond.find(selectors.trigger);
        
        this.events();
      },
      setup: function setup(){
        return true;
      }
    },
    /**
     * Carousel
     * ========
     */
    carousel: {
      options: {
        classNames:{
          prev: 'prev',
          next: 'next'
        },
        selectors: {
          container: '.slider',
          navigation: '.prev-next',
          articlesContainer: 'section.articles'
          
        },
        texts: {
          prev: 'Zur&uuml;ck',
          next: 'Vor',
        }
      },
      resize: function resize(){},
      ready: function ready(){
        var $container = $('.slider'),
            has_carousel = $container.length > 0,
            $articlesContainer = $('section.articles'),
            is_archive = $articlesContainer.length > 0;

        if ( has_carousel ) {
          (function(){
            var $navigation = $('<div class="prev-next"></div>'),
                $carousel = $('.slides', $container),
                $slides = $('.slide', $carousel),
                is_articlesContainer = $container.is('.articles'),
                scrollDuration = 1000,
                set_containerHeight = function set_containerHeight(data){

                  $container.animate({
                    width: data.width,
                    height: data.height,
                  }, {
                    duration: scrollDuration
                  });
                },
                set_activeClassName = function set_activeClassName(data){
                  var activeSlide = data.items.hasOwnProperty('visible') ? data.items.visible[0] : data.items[0]
                  $(activeSlide).addClass('active');
                },
                remove_activeClassName = function remove_activeClassName(data){
                  $(data.items.old[0]).removeClass('active');
                },
                onCreate = function onCreate(data){
                  if ( is_articlesContainer ) {
                    set_containerHeight(data);
                  }
                  set_activeClassName(data);
                },
                onBefore = function onBefore(data){
                  if ( is_articlesContainer ) {
                    set_containerHeight(data);
                  }
                  remove_activeClassName(data);
                },
                onAfter = function onAfter(data){
                  set_activeClassName(data);
                },
                options = {
                  circular: true,
                  infinite: true,
                  responsive: true,
                  swipe: true,
                  onCreate: onCreate,
                  scroll: {
                    easing: 'swing',
                    fx: is_articlesContainer ? 'scroll' : 'crossfade',
                    duration: scrollDuration,
                    pauseOnHover: true,
                    onBefore: onBefore,
                    onAfter: onAfter
                  },
                  auto: {
                    play: is_articlesContainer ? false : true,
                    duration: scrollDuration,
                    timoutDuration: 5
                  },
                  pagination: {
                    container: '.slider-nav',
                    keys: true,
                    anchorBuilder: function anchorBuilder(index){
                      return '<li><a href="#"><span>' + index + '</span></a></li>';
                    }
                  },
                  prev: {
                    button: function(){
                      var $prev = $('<a class="prev">Zur&uuml;ck</a>');

                      $prev.appendTo($navigation);
                      return $prev;
                    }
                  },
                  next: {
                    button: function(){
                      var $next = $('<a class="next">Vor</a>');

                      $next.appendTo($navigation);
                      return $next;
                    }
                  }
                };

            if ( !is_articlesContainer ) {
              $.extend(true, options, {
                items: {
                  visible: 1,
                  width: "1500",
                  height: "33%",
                },

              });
            }

            // Slider Javascript Powered
            $navigation.appendTo($container);
            $carousel.carouFredSel(options);
          })();
        }
      },
      setup: function setup(){
        var isSetup = sandboxTheme.info.is_front_page || sandboxTheme.info.is_home;
        
        return isSetup;
      }
    }
  };
  
  /**
   * Support for JSON API Plugin
   * ===========================
   * @see https://wordpress.org/plugins/json-api/
   
  var siteData = $.getJSON(window.location.href + '?json=1');
  
  siteData.done(function(pageData){
    sandboxTheme.init(pageData);
  });
   */
  
  sandboxTheme.init(sandboxTheme_data);
    
})(jQuery, Modernizr);



