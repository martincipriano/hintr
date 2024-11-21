window.hintr = {}

window.hintr.init = function() {

  let inputs = document.querySelectorAll('[type="text"][data-hintr], [type="search"][data-hintr], [name=s]')

  inputs.forEach(input => {
    let element = document.createElement('ul')
    let rect = input.getBoundingClientRect()

    element.classList.add('hintr')
    element.style.top = rect.top + input.offsetHeight + 'px'
    element.style.left = rect.left + 'px'
    element.style.width = input.offsetWidth + 'px'

    input.parentNode.insertBefore(element, input.nextSibling)
  })
}

window.hintr.toggleSuggestions = function(e) {
  let input = e.currentTarget
  let suggestions = input.nextElementSibling

  let settings = hintrSettings 
  let settingsOverride = input.getAttribute('data-hintr') ? JSON.parse(input.getAttribute('data-hintr')) : false 
  let postTypes = Object.keys(settings.search_in)

  if (input.value.length > 2) {

    if (suggestions.classList.contains('show') === false) {
      suggestions.classList.add('show')
    }

    if (settingsOverride) {
      postTypes = Object.keys(settingsOverride.search_in)
    }

    let promises = postTypes.map(postType => {
      return fetch(settings.uploadDir + postType + '.json')
        .then(response => response.json())
    })

  } else {
    suggestions.classList.remove('show')
  }
}

window.hintr.eventListeners = function() {
}

document.addEventListener('DOMContentLoaded', function() {
  window.hintr.init()
  window.hintr.eventListeners()
})
