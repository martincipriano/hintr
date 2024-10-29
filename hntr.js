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
    element.innerHTML = '<li>Item 1</li><li>Item 2</li><li>Item 3</li>'
  })
}

window.hntr.eventListeners = function() {

  // Get all inputs with data-hntr="true"
  let inputs = document.querySelectorAll('[type="text"][data-hntr="true"], [type="search"][data-hntr="true"]')

  // Add an event listener to each input element
  inputs.forEach(input => {

    // Listen to the keyup event
    input.addEventListener('keyup', function() {

      // Get the hntr list element
      let list = input.nextElementSibling

      // Check if there are 3 or more characters in the input
      if (input.value.length >= 3 && list.classList.contains('show') === false) {

        // Add the class show to the list element
        list.classList.add('show')
      }
    })
  })
}

document.addEventListener('DOMContentLoaded', function() {
  window.hntr.init()
  window.hntr.eventListeners()
})
