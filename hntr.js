window.hntr = {}

window.hntr.init = function() {

  // Get all inputs with data-hntr="true"
  let inputs = document.querySelectorAll('[type="text"][data-hntr="true"], [type="search"][data-hntr="true"]')

  // Add an element to the DOM for each input
  inputs.forEach(input => {

    // Create am unordered list
    let element = document.createElement('ul')
    let rect = input.getBoundingClientRect()

    // Add a class to the unordered list
    element.classList.add('hntr')

    // Add the top and left position of the input to the top and left position of the unordered list
    element.style.top = rect.top + input.offsetHeight + 'px'
    element.style.left = rect.left + 'px'
    element.style.width = input.offsetWidth + 'px'

    // Add the unordered list after the input element
    input.parentNode.insertBefore(element, input.nextSibling)

    // temporarily add a list item to the unordered list
    element.innerHTML = '<li><a class="hntr-nav-item" href="#">Item 1</a></li><li><a href="#">Item 2</a></li><li><a href="#">Item 3</a></li>'
  })
}

window.hntr.toggleSuggestions = function(e) {

  // Get the input element
  let input = e.currentTarget

  // Get the hntr list element
  let list = input.nextElementSibling

  // Add the class "show" if there are 3 or more characters in the input
  if (input.value.length > 3) {

    // If the suggestions hasn't been shown yet
    // Add the class show to the list element
    if (list.classList.contains('show') === false) {
      list.classList.add('show')
    }

  // Remove the class "show" if there are less than 3 characters in the input
  } else {
    list.classList.remove('show')
  }
}

window.hntr.hideSuggestions = function(e) {
  // Get the input element
  let input = e.currentTarget

  // Get the hntr list element
  let list = input.nextElementSibling

  list.classList.remove('show')
}

window.hntr.eventListeners = function() {

  // Get all inputs with data-hntr="true"
  let inputs = document.querySelectorAll('[type="text"][data-hntr="true"], [type="search"][data-hntr="true"]')

  // Add an event listener to each input element
  inputs.forEach(input => {

    // Listen to the keyup event
    input.addEventListener('keyup', window.hntr.toggleSuggestions)

    // Listen to the focusout event
    // input.addEventListener('blur', window.hntr.hideSuggestions)

    // Add an event listener that listens to the click event outside of the input element and the suggestions list
    document.addEventListener('click', function(e) {

      console.log(e.target)

      if (input !== e.target && input.nextElementSibling !== e.target) {
        input.nextElementSibling.classList.remove('show')
      }
    })
  })
}

document.addEventListener('DOMContentLoaded', function() {
  window.hntr.init()
  window.hntr.eventListeners()
})
