window.hintr = {}

window.hintr.init = function() {

  // Get all inputs with data-hintr
  let inputs = document.querySelectorAll('[type="text"][data-hintr], [type="search"][data-hintr]')

  // Add an element to the DOM for each input
  inputs.forEach(input => {

    // Create am unordered list
    let element = document.createElement('ul')
    let rect = input.getBoundingClientRect()

    // Add a class to the unordered list
    element.classList.add('hintr')

    // Add the top and left position of the input to the top and left position of the unordered list
    element.style.top = rect.top + input.offsetHeight + 'px'
    element.style.left = rect.left + 'px'
    element.style.width = input.offsetWidth + 'px'

    // Add the unordered list after the input element
    input.parentNode.insertBefore(element, input.nextSibling)
  })
}

window.hintr.toggleSuggestions = function(e) {

  // Get the input element
  let input = e.currentTarget

  // Get the hintr list element
  let list = input.nextElementSibling

  // Add the class "show" if there are 3 or more characters in the input
  if (input.value.length > 3) {

    // If the suggestions hasn't been shown yet
    // Add the class show to the list element
    if (list.classList.contains('show') === false) {
      list.classList.add('show')
    }

    // get the post types from the data-hintr attribute
    let postTypes = input.getAttribute('data-hintr').split(',')

    postTypes = postTypes.map(postType => postType.trim())

    // Fetch and combine the post type json
    let promises = postTypes.map(postType => {
      return fetch(hintrData.uploadDir + postType + '.json')
        .then(response => response.json())
    })

    Promise.all(promises)
      .then(data => {
        data = data.reduce((acc, innerObj) => {
          return { ...acc, ...innerObj }
        }, {})
        data = Object.values(data)
        data = data.filter(item => item.title.toLowerCase().includes(input.value.toLowerCase()))

        list.innerHTML = ''

        data.forEach(item => {
          list.innerHTML += hintrData.hint
            .replace('title', item.title)
            .replace('url', item.url)
        })
      })

  // Remove the class "show" if there are less than 3 characters in the input
  } else {
    list.classList.remove('show')
  }
}

window.hintr.hideSuggestions = function(e) {
  // Get the input element
  let input = e.currentTarget

  // Get the hintr list element
  let list = input.nextElementSibling

  list.classList.remove('show')
}

window.hintr.eventListeners = function() {

  // Get all inputs with data-hintr
  let inputs = document.querySelectorAll('[type="text"][data-hintr], [type="search"][data-hintr]')

  // Add an event listener to each input element
  inputs.forEach(input => {

    // Listen to the keyup event
    input.addEventListener('keyup', window.hintr.toggleSuggestions)

    // Listen to the focusout event
    // input.addEventListener('blur', window.hintr.hideSuggestions)

    // Add an event listener that listens to the click event outside of the input element and the suggestions list
    document.addEventListener('click', function(e) {


      if (input !== e.target && input.nextElementSibling !== e.target) {
        input.nextElementSibling.classList.remove('show')
      }
    })
  })
}

document.addEventListener('DOMContentLoaded', function() {
  window.hintr.init()
  window.hintr.eventListeners()
})
