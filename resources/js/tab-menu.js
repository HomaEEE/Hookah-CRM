const navEl = document.querySelector(".menu-toggle");

const handleScroll = () => {
  // Check the vertical scroll position of the document
  // window.scrollY is widely supported, or use document.documentElement.scrollTop
  const scrollPosition = window.scrollY || document.documentElement.scrollTop;

  // Define the scroll threshold (e.g., 100 pixels, or window.innerHeight for 100vh)
  const scrollThreshold = 100;

  // Use classList.toggle() with a boolean argument
  // If the second argument is true, the class is added. If false, it's removed.
  navEl.classList.toggle("nav-active", scrollPosition > scrollThreshold);
};

// Listen for the 'scroll' event on the window
window.addEventListener("scroll", handleScroll);

// Optional: call the function on load to set the initial state
handleScroll();