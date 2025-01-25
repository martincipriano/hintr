window.hintr = {}

window.hintr.init = function() {
  const inputs = document.querySelectorAll('[type="text"][data-hintr], [type="search"][data-hintr], [name=s]')

  inputs.forEach(input => {
    const element = document.createElement('ul')

    element.classList.add('hintr')

    input.parentNode.insertBefore(element, input.nextSibling)
  })

  window.hintr.updatePosition()
}

window.hintr.updatePosition = function () {
  const inputs = document.querySelectorAll('[type="text"][data-hintr], [type="search"][data-hintr], [name=s]')

  inputs.forEach(input => {
    const rect = input.getBoundingClientRect()
    const parent = input.parentElement
    const list = parent.querySelector('.hintr')

    if (list) {
      list.style.top = `${rect.top + input.offsetHeight}px`
      list.style.left = `${rect.left}px`
      list.style.width = `${rect.width}px`
    }
  })
}

window.hintr.createLocalStorage = async () => {
  const cachedPosts = localStorage.getItem('hintr')

  if (cachedPosts) {
    const cachedData = JSON.parse(cachedPosts)
    if (hintrSettings.last_updated == localStorage.getItem('hintrLastUpdated')) {
      console.log('Serving suggestions from local storage...')
      return cachedData
    }
  }

  try {
    const response = await fetch(`${hintrSettings.upload_dir}/hintr.json`);

    if (!response.ok) {
      alert('Please check wp-content/hintr.json file. It seems to be missing or not accessible.')
    }

    const data = await response.json();

    console.log('Saving suggetions to local storage...')

    localStorage.setItem('hintr', JSON.stringify(data));
    localStorage.setItem('hintrLastUpdated', hintrSettings.last_updated);

    return data;

  } catch (error) {

    console.error('Error fetching hintr.json:', error);

    return error;
  }
}

window.hintr.toggleSuggestions = function(e) {
  if (!window.hintr.toggleSuggestions.timer) {
    window.hintr.toggleSuggestions.timer = null
  }

  clearTimeout(window.hintr.toggleSuggestions.timer)

  const input = e.currentTarget

  window.hintr.toggleSuggestions.timer = setTimeout(() => {
    const suggestions = input.nextElementSibling
    const settings = hintrSettings
    const settingsOverride = input.getAttribute('data-hintr') ? JSON.parse(input.getAttribute('data-hintr')) : false
    let postTypes = Object.keys(settings.search_in)
    let count = settings.count

    if (input.value.length > 1) {
      const cachedPosts = localStorage.getItem('hintr')

      if (!suggestions.classList.contains('show')) {
        suggestions.classList.add('show')
      }

      if (settingsOverride) {
        postTypes = Object.keys(settingsOverride.search_in)
        count = settingsOverride.count
      }

      if (cachedPosts) {
        let posts = JSON.parse(cachedPosts)

        posts = posts.filter(function (item) {
          const keyword = input.value.toLowerCase()
          const title = item.title.toLowerCase()
          const metadata = item.metadata
          let condition = []

          if (!postTypes.includes(item.type)) {
            return false
          }

          if (settingsOverride && settingsOverride.search_in[item.post_type]) {
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

        suggestions.innerHTML = ''

        posts
          .slice(0, count)
          .forEach(item => {
            suggestions.innerHTML += hintrSettings.hint
              .replace('title', item.title)
              .replace('url', item.url)
          })
      } else {
        console.error('No posts found in localStorage')
      }
    } else {
      suggestions.classList.remove('show')
    }
  }, 300)
}

window.hintr.eventListeners = function() {

  let inputSelector = ['[type="text"][data-hintr]', '[type="search"][data-hintr]'];

  if (hintrSettings.auto) {
    inputSelector.push('[name=s]')
  }

  document
    .querySelectorAll(inputSelector.join(', '))
    .forEach(input => {
      input.addEventListener('keyup', window.hintr.toggleSuggestions)
      document.addEventListener('click', function(e) {
        if (input !== e.target && input.nextElementSibling !== e.target) {
          input.nextElementSibling.classList.remove('show')
        }
      })
    })

  window.addEventListener('scroll', window.hintr.updatePosition)
  window.addEventListener('resize', window.hintr.updatePosition)
}

document.addEventListener('DOMContentLoaded', function() {
  (async () => {
    let posts = await window.hintr.createLocalStorage()
    if (posts) {
      window.hintr.updatePosition()
    }
  })()

  window.hintr.init()
  window.hintr.eventListeners()
})
