import IntlData from './translations/FR';
import CommentSection from './components/Comment/CommentSection';
import OpinionPage from './components/Opinion/OpinionPage';
import AuthService from './services/AuthService';
import FeatureService from './services/FeatureService';
import CollectStepPage from './components/Page/CollectStepPage';
import SelectionStepPage from './components/Page/SelectionStepPage';
import ProposalPage from './components/Proposal/Page/ProposalPage';

FeatureService.load();

AuthService
  .login()
  .then(() => {
    // We enable React apps
    if ($('#render-idea-comments').length) {
      React.render(
        <CommentSection uri="ideas" object={$('#render-idea-comments').data('idea')} {...IntlData} />,
        document.getElementById('render-idea-comments')
      );
    }

    if ($('#render-post-comments').length) {
      React.render(
        <CommentSection uri="posts" object={$('#render-post-comments').data('post')} {...IntlData} />,
        document.getElementById('render-post-comments')
      );
    }

    if ($('#render-event-comments').length) {
      React.render(
        <CommentSection uri="events" object={$('#render-event-comments').data('event')} {...IntlData} />,
        document.getElementById('render-event-comments')
      );
    }

    if ($('#render-opinion').length) {
      React.render(
        <OpinionPage
          opinionId={$('#render-opinion').data('opinion')}
          {...IntlData}
        />,
        document.getElementById('render-opinion')
      );
    }

    if ($('#render-proposal-page').length) {
      React.render(
        <ProposalPage
          proposal={$('#render-proposal-page').data('proposal').proposal}
          form={$('#render-proposal-page').data('form').form}
          themes={$('#render-proposal-page').data('themes').themes}
          districts={$('#render-proposal-page').data('districts').districts}
          {...IntlData}
        />,
        document.getElementById('render-proposal-page')
      );
    }

    if ($('#render-collect-step-page').length) {
      React.render(
        <CollectStepPage
          count={$('#render-collect-step-page').data('count')}
          form={$('#render-collect-step-page').data('form').form}
          themes={$('#render-collect-step-page').data('themes').themes}
          districts={$('#render-collect-step-page').data('districts').districts}
          types={$('#render-collect-step-page').data('types').types}
          statuses={$('#render-collect-step-page').data('statuses').statuses}
          {...IntlData}
        />,
        document.getElementById('render-collect-step-page')
      );
    }

    if ($('#render-selection-step-page').length) {
      React.render(
        <SelectionStepPage
          count={$('#render-selection-step-page').data('count')}
          stepId={$('#render-selection-step-page').data('step-id')}
          votable={$('#render-selection-step-page').data('votable')}
          themes={$('#render-selection-step-page').data('themes').themes}
          districts={$('#render-selection-step-page').data('districts').districts}
          types={$('#render-selection-step-page').data('types').types}
          statuses={$('#render-selection-step-page').data('statuses').statuses}
          {...IntlData}
        />,
        document.getElementById('render-selection-step-page')
      );
    }

    if ($('#render-opinion-version').length) {
      React.render(
        <OpinionPage
          opinionId={$('#render-opinion-version').data('opinion')}
          versionId={$('#render-opinion-version').data('version')}
          {...IntlData}
        />,
        document.getElementById('render-opinion-version')
      );
    }
  });

// Our global App for symfony
const App = (($) => {
  const equalheight = (container) => {
    let currentTallest = 0;
    let currentRowStart = 0;
    const rowDivs = [];
    let $el;
    let topPosition = 0;

    $(container).each(function() {
      $el = $(this);
      $($el).height('auto');
      topPosition = $el.position().top;

      if ($(window).width() > 767) {
        let currentDiv = 0;

        if (currentRowStart !== topPosition) {
          for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
            rowDivs[currentDiv].height(currentTallest);
          }
          rowDivs.length = 0; // empty the array
          currentRowStart = topPosition;
          currentTallest = $el.height();
          rowDivs.push($el);
        } else {
          rowDivs.push($el);
          currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
        }
        for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
          rowDivs[currentDiv].height(currentTallest);
        }
      }
    });
  };

  const resized = (el) => {
    const $el = $(el);

    $(window).resize(() => {
      equalheight($el);
    });
  };

  const customModal = (el) => {
    const $el = $(el);

    $el.appendTo('body');
  };

  const pieChart = () => {
    if (typeof(google) !== 'undefined') {
      google.load('visualization', '1', {packages: ['corechart']});
      google.setOnLoadCallback(() => {
        $('.has-chart').googleCharts();
      });
    }
  };

  const video = (el) => {
    const $el = $(el);
    $el.on('click', () => {
      $.fancybox({
        href: this.href,
        type: $(this).data('type'),
        padding: 0,
        margin: 50,
        maxWidth: 1280,
        maxHeight: 720,
        fitToView: false,
        width: '90%',
        height: '90%',
      }); // fancybox
      return false;
    }); // on
  };

  const checkButton = (el) => {
    const $el = $(el);

    $($el).on('change', () => {
      const test = $(this).val();
      if (test === 0) {
        $('.block_media').hide();
        $('.block_link').toggle();
      } else if (test === 1) {
        $('.block_media').toggle();
        $('.block_link').hide();
      }
    });
  };

  const externalLinks = () => {
    $(document).on('click', '.external-link', () => {
      window.open($(this).attr('href'));
      return false;
    });
  };

  const showMap = (container) =>{
    const $mapCanvas = $(container);
    $mapCanvas.each(() => {
      // Map
      const mapOptions = {
        center: new google.maps.LatLng($(this).attr('data-lat'), $(this).attr('data-lng')),
        zoom: 15,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
      };
      const map = new google.maps.Map(this, mapOptions);

      // Marker
      const marker = new google.maps.Marker({
        position: new google.maps.LatLng($(this).attr('data-lat'), $(this).attr('data-lng')),
      });
      marker.setMap(map);
    });
  };

  const navbarAutocollapse = (label) => {
    $('#navbar-content').append($('#navbar-content li.hideshow ul').html());
    $('#navbar-content li.hideshow').remove();

    if (window.matchMedia('(min-width: 768px)').matches) {
      const occupiedWidth = $('.navbar-header').width() + $('.navbar-right').width() + 80;
      const maxWidth = $('#main-navbar > .container').width() - occupiedWidth;
      let menuHtml = '';

      let width = 0;
      $('#navbar-content').children().each(() => {
        width += $(this).outerWidth(true);
        if (maxWidth < width) {
          // Get outer html of children element
          menuHtml += $(this).clone().wrap('<div>').parent().html();
          $(this).remove();
        }
      });

      $('#navbar-content').append(
        '<li class="hideshow dropdown">'
        + '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' + label + ' <span class="caret"></span></a>'
        + '<ul class="dropdown-menu">' + menuHtml + '</ul>'
        + '</li>'
      );

      $('#navbar-content li.hideshow').on('click', '.dropdown-menu', (e) => {
        if ($(this).parent().is('.open')) {
          e.stopPropagation();
        }
      });

      if (menuHtml === '') {
        $('#navbar-content li.hideshow').hide();
      } else {
        $('#navbar-content li.hideshow').show();
      }
    }
  };

  const initPopovers = (triggers, options) => {
    const $triggers = $(triggers);
    $triggers.attr('tabindex', '0');
    $triggers.popover(options).on('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
    });
  };

  const makeSidebar = (options) => {
    // Fix containers
    const containers = options.container + ' .container';
    $(options.container).addClass('container  sidebar__container');
    $(containers).removeClass('container  container--thinner').addClass('container--with-sidebar');

    // Handle small screens
    $(options.toggle).on('click', () => {
      $(options.hideable).toggleClass('sidebar-hidden-small');
      $(options.overlay).toggleClass('sidebar__darkened-overlay');
    });
  };

  const carousel = () => {
    $('.carousel-sidenav li').on('click', (e) => {
      e.preventDefault();
      $('.carousel-sidenav li').each(() => {
        $(this).removeClass('active');
      });
      $(this).addClass('active');
    });
  };

  const hideableMessageAndCheckbox = (options) => {
    const messageDiv = options.message;
    const messageField = messageDiv + ' textarea';
    const checkboxDiv = options.checkbox;
    const checkboxField = checkboxDiv + ' input[type="checkbox"]';
    let oldVal = null;

    $(messageField).on('change keyup paste', () => {
      const currentVal = $(this).val();
      if (currentVal === oldVal) {
        return;
      }
      oldVal = currentVal;
      if (currentVal) {
        $(checkboxDiv).addClass('hidden');
        return;
      }
      $(checkboxDiv).removeClass('hidden');
    });

    $(checkboxField).on('change', () => {
      if ($(this).prop('checked')) {
        $(messageDiv).addClass('hidden');
        return;
      }
      $(messageDiv).removeClass('hidden');
    });
  };

  const skipLinks = () => {
    $('.js-skip-links a').on('focus', () => {
      $('.js-skip-links').addClass('active');
      $('body').css('margin-top', $('.js-skip-links').height());
    });
    $('.js-skip-links a').on('blur', () => {
      $('.js-skip-links').removeClass('active');
      $('body').css('margin-top', '0');
    });
  };

  return {
    equalheight: equalheight,
    resized: resized,
    pieChart: pieChart,
    checkButton: checkButton,
    video: video,
    externalLinks: externalLinks,
    showMap: showMap,
    navbarAutocollapse: navbarAutocollapse,
    initPopovers: initPopovers,
    makeSidebar: makeSidebar,
    carousel: carousel,
    hideableMessageAndCheckbox: hideableMessageAndCheckbox,
    customModal: customModal,
    skipLinks: skipLinks,
  };
})(jQuery);

export default App;
