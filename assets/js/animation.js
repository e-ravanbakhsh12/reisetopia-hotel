class MainAnimation {
  constructor() {}

  once(selector) {
    // new LenisScroll(selector);
  }

  pluginsHandler(selector) {
    // new InfinitySlider(selector);
  }

  animationsHandler(selector) {
    this.animationsArray = [];
    this.animElements = [...selector.querySelectorAll("[data-anim]")];
    this.animationsList = [
      "up",
      "horizontal",
    ];

    this.animationsList.forEach((animation) => {
      this.animElements.forEach((el) => {
        const anim = el.dataset.anim;
        console.log(animation);
        if (animation === anim && anim === "up") {
          this.animationsArray.push(new AnimUp(el));
        }

        if (animation === anim && anim === "horizontal") {
          this.animationsArray.push(new AnimHorizontal(el));
        }
      });
    });
  }
}

class Animations {
  constructor({ element }) {
    this.element = element;
    this.dataset = element.dataset;
    this.scope = gsap.utils.selector(this.element);
    this.targets = this.dataset.targets
      ? this.dataset.targets.includes(",")
        ? this.dataset.targets.split(",").map((el) => this.scope(el))
        : this.scope(this.dataset.targets)
      : this.element;

    this.split = this.dataset.split
      ? this.SplitHandler(
          this.targets,
          this.dataset.split,
          this.dataset.depth || 2
        )
      : false;
    this.scrub = this.dataset.scrub === "" ? true : false;

    if (this.dataset.split) this.targets = this.scope(`.${this.dataset.split}`);
    this.prepare();

    if (document.querySelector(".loader")) {
      window.addEventListener("loaderFinished", (e) => {
        this.scrollTrigger();
      });
    } else {
      if (document.querySelector("header").classList.contains("menu-open")) {
        setTimeout(() => {
          this.scrollTrigger();
        }, 500);
      } else {
        this.scrollTrigger();
      }
    }
  }
  SplitHandler(el, type, depth = 1) {
    new SplitText(el, {
      type: type,
      linesClass: type,
      charsClass: type,
      wordsClass: type,
      reduceWhiteSpace: false,
    });
    if (depth > 1) {
      new SplitText(el, {
        type: "lines",
        linesClass: "split-wrapper",
        reduceWhiteSpace: false,
      });
    }
  }

  scrollTrigger() {
    ScrollTrigger.create({
      trigger: this.element,
      // markers: this.scrub && true,s
      start: this.dataset.start ? this.dataset.start : `top 100%`,
      end: this.dataset.end ? this.dataset.end : `bottom 0`,
      scrub: this.scrub && 0,
      animation: this.scrub && this.onEnter(),
      onEnter: () => !this.scrub && this.onEnter(),
      onEnterBack: () => !this.scrub && this.onEnterBack(),
    });

    ScrollTrigger.create({
      trigger: this.element,
      start: `top ${this.dataset.backStart ?? "110%"}`,
      end: `bottom ${this.dataset.backEnd ?? "-10%"}`,
      onLeave: () => !this.scrub && this.onLeave(),
      onLeaveBack: () => !this.scrub && this.prepare(),
    });
  }

  onEnter() {}

  onEnterBack() {
    //   gsap.to(this.targets, {
    //     opacity: 1,
    //     y: 0,
    //     duration: 1,
    //     stagger: -0.08,
    //     delay: 0.05,
    //     ease: 'power3.out',
    //     overwrite: true,
    //   });
  }

  onLeave() {
    //   gsap.set(this.targets, {
    //     opacity: 0,
    //     y: -30,
    //     x: 0,
    //     duration: 1,
    //     stagger: 0.08,
    //     ease: 'power3.out',
    //     overwrite: true,
    //   });
  }

  onLeaveBack() {}
}


class AnimUp extends Animations {
  constructor(element) {
    super({
      element,
    });
  }

  prepare() {
    gsap.set(this.targets, {
      opacity: 0,
      y: this.dataset.y || "50",
      overwrite: true,
    });
  }

  onEnter() {
    gsap.to(this.targets, {
      y: 0,
      opacity: 1,
      delay: this.dataset.delay,
      duration: this.dataset.durr || 1,
      stagger: this.dataset.stagger || 0.09,
      ease: "power3.out",
    });
  }
}

class AnimHorizontal extends Animations {
  constructor(element) {
    super({
      element,
    });
  }

  prepare() {
    gsap.set(this.targets, {
      opacity: this.scrub ? 1 : 0,
      x: this.dataset.x || -50,
      overwrite: true,
    });
  }

  onEnter() {
    return gsap.to(this.targets, {
      opacity: 1,
      x: this.dataset.to || 0,
      delay: this.dataset.delay,
      duration: this.scrub ? 2 : 1,
      stagger: window.innerWidth < 1024 ? 0.01 : this.dataset.stagger || 0.1,
      ease: this.scrub ? "none" : "power3.out",
    });
  }
}

window.addEventListener("DOMContentLoaded", (e) => {
  gsap.registerPlugin(ScrollTrigger);

  let animation = new MainAnimation();

  animation.pluginsHandler(document);
  document.fonts.ready.then(() => {
    animation.animationsHandler(document);
  });
});
window.addEventListener("queryLoaded", (e) => {
  console.log(e);
  let animation = new MainAnimation();

  animation.animationsHandler(e.detail.container);
  animation.pluginsHandler(e.detail.container);
});
