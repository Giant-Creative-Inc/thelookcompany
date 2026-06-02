const { src, dest, watch, series, parallel } = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const cleanCSS = require("gulp-clean-css");
const rename = require("gulp-rename");
//const plumber = require('gulp-plumber');

const outDir = "./assets/css";

function build(file, outName) {
  return function compile() {
    return (
      src(file)
        //.pipe(plumber())                // keeps the stream alive on errors
        .pipe(sass().on("error", sass.logError))
        .pipe(cleanCSS({ compatibility: "ie11" }))
        .pipe(rename(outName))
        .pipe(dest(outDir))
    );
  };
}

// individual tasks
const cssMain = build("./assets/scss/main.scss", "style.min.css");
const cssHome = build("./assets/scss/pages/home.scss", "home.min.css");
const cssContact = build("./assets/scss/pages/contact.scss", "contact.min.css");
const cssOurCompanies = build("./assets/scss/pages/our-company.scss", "our-company.min.css");
const cssMeetOurTeam = build("./assets/scss/pages/meet-our-team.scss", "meet-our-team.min.css");
const cssSportsAndEvents = build('./assets/scss/pages/sports-and-events.scss', 'sports-and-events.min.css');
const cssRetailSolutions = build('./assets/scss/pages/retail-solutions.scss', 'retail-solutions.min.css');
const cssThankYou = build("./assets/scss/pages/thank-you.scss", "thank-you.min.css");
const cssBlog = build("./assets/scss/pages/blog.scss", "blog.min.css");
const cssBlogCategory = build( "./assets/scss/pages/blog-category.scss", "blog-category.min.css");
const cssFaq = build("./assets/scss/pages/faq.scss", "faq.min.css");
const cssOurWork = build("./assets/scss/pages/our-work.scss", "our-work.min.css");
const cssVideos = build("./assets/scss/pages/videos.scss", "videos.min.css");
const cssGuides = build("./assets/scss/pages/guides.scss", "guides.min.css");
const cssBrandActivations = build("./assets/scss/pages/brand-activations.scss", "brand-activations.min.css");
const cssTheLookGroup = build("./assets/scss/pages/the-look-group.scss", "the-look-group.min.css");
const cssCareers = build("./assets/scss/pages/careers.scss", "careers.min.css");
const cssLightboxes = build("./assets/scss/pages/lightboxes.scss", "lightboxes.min.css");
const cssFabricAndFrames = build("./assets/scss/pages/fabric-and-frames.scss", "fabric-and-frames.min.css");
const cssDisplaySystems = build("./assets/scss/pages/display-systems.scss", "display-systems.min.css");
const cssSignsAndBanners = build("./assets/scss/pages/signs-and-banners.scss", "signs-and-banners.min.css");
const cssServices = build("./assets/scss/pages/services.scss", "services.min.css");

// group task
const styles = parallel(
  cssMain,
  cssHome,
  cssThankYou,
  cssContact,
  cssOurCompanies,
  cssMeetOurTeam,
  cssBlog,
  cssBlogCategory,
  cssSportsAndEvents,
  cssRetailSolutions,
  cssFaq,
  cssOurWork,
  cssVideos,
  cssGuides,
  cssBrandActivations,
  cssTheLookGroup,
  cssCareers,
  cssLightboxes,
  cssFabricAndFrames,
  cssDisplaySystems,
  cssSignsAndBanners,
  cssServices
);

// watcher
function watcher() {
  watch("./assets/scss/**/*.scss", styles);
}

exports.styles = styles;
exports.default = series(styles, watcher);
