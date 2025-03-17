document.querySelector(".stagelink").addEventListener("click", function () {
  const easterEgg = document.querySelector(".easter-egg");
  easterEgg.style.display = "flex";
  setTimeout(() => {
    easterEgg.style.display = "none";
  }, 3500);
});
