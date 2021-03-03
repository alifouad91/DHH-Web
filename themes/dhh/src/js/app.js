require("jquery-mousewheel")($);
require("malihu-custom-scrollbar-plugin")($);
import AOS from "aos";
import "aos/dist/aos.css";

import Header from "./components/Header";
import Maps from "./components/Maps";

import config from "./react/config";

import "./external/promise-polyfill";
import "./external/qs";

import "react-dates/initialize";
import "react-dates/lib/css/_datepicker.css";
import "./react";

import "./external/bootstrap/bootstrap.min.css";

import "./external/chosen";
import "./external/chosen/style.css";

import "./external/fancybox";
import "./external/fancybox/style.css";

import "./external/intl";
import "./external/intl/style.css";
// import { toQueryString } from "./react/utils";

export default new class App {
  constructor() {
    this.setDomMap();
    this.previousScroll = 0;

    // dom ready shorthand
    $(() => {
      this.domReady();
    });
  }

  domReady = () => {
    if (!Boolean(CCM_IS_USER_LOGGED_IN)) {
      localStorage.removeItem("access_token");
    }
    this.initComponents();
    this.handleUserAgent();
    this.windowResize();
    this.bindEvents();
    this.handleSplashScreen();
    this.initExternalLibraries();
  };

  initComponents = () => {
    new Header({
      header: this.header,
      htmlBody: this.htmlBody,
    });

    if (this.mapContainer.length) {
      new Maps();
    }
  };

  initExternalLibraries = () => {
    const video = document.getElementById("vimeo-video");
    if (video) {
      video.muted = true;
      video.loop = true;
      video.play();
    }

    AOS.init({
      duration: 800,
      once: true,
    });
    $(".ant-form-item-control.select select").chosen({
      disable_search_threshold: 20000,
    });

    // let intel =  IntlTelInput($(".ccm-input-tel"), {
    //   preferredCountries: ['ae'],
    //   formatOnDisplay: true,
    // });

    $(".ccm-input-tel").intlTelInput({
      allowExtensions: true,
      formatOnDisplay: true,
      autoFormat: true,
      autoHideDialCode: true,
      autoPlaceholder: false,
      defaultCountry: "auto",
      nationalMode: false,
      numberType: "MOBILE",
      initialCountry: "ae",
      preventInvalidNumbers: true,
      geoIpLookup: (callback) => {
        // $.get("https://ipinfo.io", () => {}, "jsonp").always(resp => {
        //   const countryCode = resp && resp.country ? resp.country : "";
        //   callback(countryCode);
        // });
      },
    });
    $(".ccm-input-tel")
      .closest(".ant-form-item-children")
      .prev()
      .addClass("active");
  };

  setDomMap = () => {
    this.window = $(window);
    this.htmlNbody = $("body, html");
    this.html = $("html");
    this.htmlBody = $("body");
    this.siteLoader = $(".site-loader");
    this.header = $("header");
    this.siteBody = $(".site-body");
    this.footer = $("footer");
    this.gotoTop = $("#gotoTop");
    this.gRecaptcha = $(".g-recaptcha");
    this.wrapper = $(".wrapper");
    this.pushDiv = this.wrapper.find(".push");
    this.mapContainer = $("#map_canvas");
    this.inputs = $(".ant-form.formidable")
      .find("input, textarea")
      .not('[type="checkbox"], [type="radio"]');
    this.radio = $(".ant-form.formidable").find(".radio:not(.element)");
    this.checkbox = $(".styled-checkbox + label");
    this.mobileMenu = $(".mobile-menu");
  };

  bindEvents = () => {
    const termsAgreed = localStorage.getItem("terms_agreed");
    if (termsAgreed) {
      $(".styled-checkbox + label")
        .prev()
        .addClass("checked");
      $(".btn-register").attr("disabled", false);
    }
    localStorage.removeItem("terms_agreed");
    // Window Events
    this.window.resize(this.windowResize).scroll(this.windowScroll);

    // General Events
    const $container = this.wrapper;
    $container.on("click", ".mobile-menu-icon", (e) => {
      const $this = $(e.currentTarget);
      $this.find("svg").toggleClass("active");
      this.mobileMenu.toggleClass("open");
      this.htmlBody.toggleClass("menu-open");
    });
    $container.on("click", ".disabled", () => false);
    const queryString = qs.get();

    if (queryString.keywords) {
      $container
        .find("#header-search")
        .val(queryString.keywords.split("%20").join(" "));
    }
    $container.on("keyup", "#header-search", (e) => {
      const $this = $(e.currentTarget);
      if (e.which === 13 && $this.val()) {
        location.href = `${config.BASE_URL}/properties?keywords=${$this.val()}`;
      }
    });
    // Specific Events
    this.gotoTop.on("click", () => {
      this.htmlNbody.animate({
        scrollTop: 0,
      });
    });
    $(".scroll-to-top").on("click", () => {
      this.htmlNbody.animate({
        scrollTop: 0,
      });
    });

    $(".styled-checkbox + label").on("click", (e) => {
      const $this = $(e.currentTarget);
      $this.prev().toggleClass("checked");
      const $prev = $this.prev();
      const isChecked = $prev.hasClass("checked");
      const $register = $(".btn-register");
      if ($prev.attr("id") === "acceptTerms") {
        $register.attr("disabled", !isChecked);
        localStorage.setItem("terms_agreed", isChecked);
      }
    });

    this.radio.on("click", (e) => {
      const $this = $(e.currentTarget);
      $this.siblings().removeClass("ant-radio-button-wrapper-checked");
      $this.addClass("ant-radio-button-wrapper-checked");
    });
    this.inputs
      .on("focus", function() {
        console.log("ss");
        $(this)
          .closest(".ant-form-item-control")
          .find(".ant-form-item-label")
          .addClass("active");
      })
      .on("blur change", function() {
        if ($(this).val() !== "") {
          $(this)
            .closest(".ant-form-item-control")
            .find(".ant-form-item-label")
            .addClass("active");
        } else {
          $(this)
            .closest(".ant-form-item-control")
            .find(".ant-form-item-label")
            .removeClass("active");
        }
      })
      .trigger("blur");

    // stop right click for privacy policy page
    if (
      $("body").hasClass("privacy-page") ||
      $("body").hasClass("terms-page")
    ) {
      $(document).bind("contextmenu", function(e) {
        return false;
      });

      $("body").bind("copy paste cut drag drop", function(e) {
        e.preventDefault();
      });
      document.onkeydown = function(e) {
        return false;
      };
      delete window.console;
    }

    // Reload the current path when changing language instead of redirecting to landing page
    // Uncomment below and modify languages
    // $container.on('click', '.language-toggle', function(e) {
    //   e.preventDefault();
    //   const $this = $(this);
    //   const href = $this.attr('href');
    //   const isEnglish = href.indexOf('/ar') >= 0;
    //   const locArray = location.pathname.split('/');
    //   const indexOfIndex = locArray.indexOf('index.php');
    //   const isDev = indexOfIndex >= 0;
    //   const index = isDev ? indexOfIndex + 1 : 1;
    //   if(!isEnglish) {
    //     locArray = locArray.filter(item => item !== 'ar')
    //   }
    //   locArray.splice(index, 0, isEnglish ? 'ar' : '');
    //   const newHref = locArray.join('/').replace('//', '/');
    //   location.href = newHref;
    // });

    // Uncomment below if you need to add google captcha (also in includes/script.php)
    // => Make sure the SITEKEY is changed below
    // this.gRecaptcha.each((index, el) => {
    //   grecaptcha.render(el, {'sitekey' : '6LeB3QwUAAAAADQMo87RIMbq0ZnUbPShlwCPZDTv'});
    // });
  };

  windowResize = () => {
    this.screenWidth = this.window.width();
    this.screenHeight = this.window.height();

    // calculate footer height and assign it to wrapper and push/footer div
    this.footerHeight = this.footer.outerHeight();
    // this.wrapper.css('margin-bottom', -this.footerHeight);
    this.pushDiv.height(this.footerHeight);
  };

  windowScroll = () => {
    if (this.screenWidth < 768 && false) {
      const topOffset = this.window.scrollTop();
      this.header.toggleClass("top", topOffset > 100);
      this.header.toggleClass("sticky", topOffset > 100);
      if (topOffset > this.previousScroll || topOffset < 250) {
        this.header.removeClass("sticky");
      } else if (topOffset < this.previousScroll) {
        this.header.addClass("sticky");
        // Additional checking so the header will not flicker
        if (topOffset > 250) {
          this.header.addClass("sticky");
        } else {
          this.header.removeClass("sticky");
        }
      }
      this.previousScroll = topOffset;
    }
    // this.gotoTop.toggleClass(
    //   "active",
    //   this.window.scrollTop() > this.screenHeight / 2
    // );
  };

  handleSplashScreen() {
    this.htmlBody.find(".logo-middle").fadeIn(500);
    this.siteLoader.delay(1500).fadeOut(500);
  }

  handleUserAgent = () => {
    // detect mobile platform
    if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
      this.htmlBody.addClass("ios-device");
    }
    if (navigator.userAgent.match(/Android/i)) {
      this.htmlBody.addClass("android-device");
    }

    // detect desktop platform
    if (navigator.appVersion.indexOf("Win") !== -1) {
      this.htmlBody.addClass("win-os");
    }
    if (navigator.appVersion.indexOf("Mac") !== -1) {
      this.htmlBody.addClass("mac-os");
    }

    // detect IE 10 and 11P
    if (
      navigator.userAgent.indexOf("MSIE") !== -1 ||
      navigator.appVersion.indexOf("Trident/") > 0
    ) {
      this.html.addClass("ie10");
    }

    // detect IE Edge
    if (/Edge\/\d./i.test(navigator.userAgent)) {
      this.html.addClass("ieEdge");
    }

    // Specifically for IE8 (for replacing svg with png images)
    if (this.html.hasClass("ie8")) {
      const imgPath = "/themes/theedge/images/";
      $("header .logo a img,.loading-screen img").attr(
        "src",
        `${imgPath}logo.png`
      );
    }

    // show ie overlay popup for incompatible browser
    if (this.html.hasClass("ie9")) {
      const message = $(
        '<div class="no-support"> You are using outdated browser. Please <a href="https://browsehappy.com/" target="_blank">update</a> your browser or <a href="https://browsehappy.com/" target="_blank">install</a> modern browser like Google Chrome or Firefox.<div>'
      );
      this.htmlBody.prepend(message);
    }
  };
}();
