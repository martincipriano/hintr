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

    Promise.all(promises)
      .then(data => {
        data = data.reduce((acc, innerObj) => {
          return { ...acc, ...innerObj }
        }, {})
        data = Object.values(data)
        data = data.filter(function(item) {

          let condition = []
          let keyword = input.value.toLowerCase()
          let title = item.title.toLowerCase()
          let metadata = item.metadata

          if (settingsOverride) {

            if (typeof settingsOverride.search_in[item.post_type] === 'undefined')
              return;

            Object.keys(metadata).forEach(key => {
              if (!settingsOverride.search_in[item.post_type].includes(key)) {
                delete metadata[key]
              }
            })
          }

          for (let key in metadata) {
            if (metadata.hasOwnProperty(key)) {
              condition.push(metadata[key].toLowerCase().includes(keyword))
            }
          }

          condition.push(title.includes(keyword))

          return condition.includes(true)
        })
      })

  } else {
    suggestions.classList.remove('show')
  }
}

window.hintr.hideSuggestions = function(e) {
  let input = e.currentTarget
  let list = input.nextElementSibling

  list.classList.remove('show')
}

window.hintr.eventListeners = function() {
}

document.addEventListener('DOMContentLoaded', function() {
  window.hintr.init()
  window.hintr.eventListeners()
})
