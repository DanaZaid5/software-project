const header = document.getElementById('siteHeader');
function onScroll(){
  if (window.scrollY > 80) header.classList.add('show');
  else header.classList.remove('show');
}
document.addEventListener('scroll', onScroll);
onScroll();
