import { Draggable } from 'gsap/Draggable';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { CSSRulePlugin } from 'gsap/CSSRulePlugin';

gsap.registerPlugin(ScrollTrigger, CSSRulePlugin);
  
  gsap.to("#Welcome", {
    backgroundColor: "#000000",
    scrollTrigger: {
      trigger: "#Welcome",   
      start: "top top",       
      end: "bottom top",      
      scrub: true,            
    }
  });



gsap.registerPlugin(Draggable);

// Slide variables
let slideDelay = 3;
let slideDuration = 1;
let wrap = true;

const no = document.querySelector("#no");
const slides = document.querySelectorAll('.slide');
const prevButton = document.querySelector('#prevButton');
const nextButton = document.querySelector('#nextButton');
const slidesContainer = document.querySelector('.slides-container');

let numSlides = slides.length;

// Position Slide
gsap.set(slides, {
  xPercent: (i) => i * 100,
  zIndex: (i) => i === 0 ? 10 : 0  // The first slide is on top, others behind
});



const wrapX = gsap.utils.wrap(-100, (numSlides - 1) * 100);
const progressWrap = gsap.utils.wrap(0, 1);

let proxy = document.createElement("div");
let slideAnimation = gsap.to({}, {});
let slideWidth = 0;
let wrapWidth = 0;

let timer = gsap.delayedCall(slideDelay, autoPlay);


let animation = gsap.to(slides, {
  xPercent: "+=" + (numSlides * 100),
  duration: 1,
  ease: "none",
  paused: true,
  repeat: -1,
  modifiers: {
    xPercent: wrapX
  }
});


const isMobile = window.innerWidth <= 768; 


let draggable;
if (!isMobile) {
  draggable = new Draggable(proxy, {
    trigger: slidesContainer,
    inertia: false, 
    onPress: updateDraggable,
    onDrag: updateProgress,
    onThrowUpdate: updateProgress,
    snap: {
      x: snapX
    }
  });
}

resize();  


window.addEventListener('resize', resize);
prevButton.addEventListener('click', () => animateSlides(1));
nextButton.addEventListener('click', () => animateSlides(-1));


function updateDraggable() {
  timer.restart(true);
  slideAnimation.kill();
  this.update();
}


function animateSlides(direction) {
  timer.restart(true);
  slideAnimation.kill();

  let currentIndex = Math.round(gsap.getProperty(proxy, 'x') / slideWidth) * -1;
  let newIndex = currentIndex + direction;

  // Ensure the newIndex wraps correctly between 0 and numSlides - 1
  newIndex = gsap.utils.wrap(0, numSlides, newIndex);

  // Set the zIndex of the current slide to 0 (behind others)
  gsap.set(slides[currentIndex], {
    zIndex: 0
  });

  // Set the zIndex of the new slide to a higher value (on top)
  gsap.set(slides[newIndex], {
    zIndex: 10
  });

  // Continue with the sliding animation
  let x = snapX(gsap.getProperty(proxy, 'x') + direction * slideWidth);

  slideAnimation = gsap.to(proxy, {
    x: x,
    duration: slideDuration,
    onUpdate: updateProgress,
    onComplete: updateSlideNumber
  });
}

function updateSlideNumber() {
    // Calculate the current slide by normalizing the proxy's x position
    let currentSlide = Math.round(gsap.getProperty(proxy, 'x') / slideWidth) * -1;

    // Wrap the current slide number within the range [0, numSlides-1]
    currentSlide = gsap.utils.wrap(0, numSlides, currentSlide);

    // Display as a 1-based index (i.e., 1/5 instead of 0/5)
    no.textContent = `${currentSlide + 1}/${numSlides}`;
}



// Autoplay
function autoPlay() {
  if (draggable && (draggable.isPressed || draggable.isDragging)) {
    timer.restart(true);
  } else {
    animateSlides(-1);
  }
}


function updateProgress() {
  animation.progress(progressWrap(gsap.getProperty(proxy, 'x') / wrapWidth));
}


function snapX(value) {
  let snapped = gsap.utils.snap(slideWidth, value);
  return wrap ? snapped : gsap.utils.clamp(-slideWidth * (numSlides - 1), 0, snapped);
}

// Handle resizing of the window and slides
function resize() {
  let norm = (gsap.getProperty(proxy, 'x') / wrapWidth) || 0;

  slideWidth = slides[0].offsetWidth;
  wrapWidth = slideWidth * numSlides;

  gsap.set(proxy, {
    x: norm * wrapWidth
  });

  // Set initial zIndex and xPercent without changing opacity
  gsap.set(slides, {
    xPercent: (i) => i * 100,
    zIndex: (i) => i === 0 ? 10 : 0
  });

  animateSlides(0);
  slideAnimation.progress(1);
}




gsap.to(".navigationWelcome", {
  backgroundColor: "#ffffff", 
  scrollTrigger: {
    trigger: "#section1Welcome",       
    start: "bottom top",    
    toggleActions: "play none none reverse", 
    markers: false          
  }
});
gsap.to(".navigationWelcome a", {
  color: "#000000", 
  scrollTrigger: {
    trigger: "#section1Welcome",       
    start: "bottom top",    
    toggleActions: "play none none reverse", 
    markers: false          
  }
});
gsap.to(".navigationWelcome h1", {
  color: "#000000", 
  scrollTrigger: {
    trigger: "#section1Welcome",       
    start: "bottom top",    
    toggleActions: "play none none reverse", 
    markers: false          
  }
});




const scrollTriggerSettings = {
  trigger: "#Welcome2",
  start: "top 25%",
  toggleActions: "play reverse play reverse"
};

const leftXValues = [-800, -900, -400];
const rightXValues = [800, 900, 400];
const leftRotationValues = [-30, -20, -35];
const rightRotationValues = [30, 20, 35];
const yValues = [100, -150, -400];

gsap.utils.toArray(".row").forEach((row, index) => {
  const cardLeft = row.querySelector(".card-left");
  const cardRight = row.querySelector(".card-right");

  // Animate the left card
  gsap.to(cardLeft, {
    x: leftXValues[index],
    y: yValues[index],
    rotation: leftRotationValues[index],
    scrollTrigger: {
      trigger: "#Welcome2",
      start: "top center",
      end: "150% bottom",
      scrub: true,
      onUpdate: (self) => {
        const progress = self.progress;
        cardLeft.style.transform = `translateX(${progress * leftXValues[index]}px) translateY(${progress * yValues[index]}px) rotate(${progress * leftRotationValues[index]}deg)`;
      },
    }
  });

  // Animate the right card
  gsap.to(cardRight, {
    x: rightXValues[index],
    y: yValues[index],
    rotation: rightRotationValues[index],
    scrollTrigger: {
      trigger: "#Welcome2",
      start: "top center",
      end: "150% bottom",
      scrub: true,
      onUpdate: (self) => {
        const progress = self.progress;
        cardRight.style.transform = `translateX(${progress * rightXValues[index]}px) translateY(${progress * yValues[index]}px) rotate(${progress * rightRotationValues[index]}deg)`;
      },
    }
  });
});

// Animate line paragraphs
gsap.to(".line p", {
  y: 0,
  stagger: 0.1,
  duration: 0.5,
  ease: "power1.out",
  scrollTrigger: scrollTriggerSettings,
});

// Animate opening class
gsap.to(".opening", {
  y: 0,
  opacity: 1,
  delay: 0.25,
  duration: 0.5,
  ease: "power1.out",
  scrollTrigger: scrollTriggerSettings,
});
