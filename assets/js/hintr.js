window.hintr = {}

window.hintr.init = function() {
  let inputs = document.querySelectorAll('[type="text"][data-hintr], [type="search"][data-hintr], [name=s]')

  inputs.forEach(input => {
    let element = document.createElement('ul')
    let rect = input.getBoundingClientRect()

    element.classList.add('hintr')
    element.style.top = rect.top + input.offsetHeight + window.scrollY + 'px'
    element.style.left = rect.left + 'px'
    element.style.width = input.offsetWidth + 'px'

    input.parentNode.insertBefore(element, input.nextSibling)
  })
}

window.hintr.createLocalStorage = async () => {
  const endpoint = '/wp-json/hintr/v1/posts'
  const perPage = 100

  let page = 1
  let posts = []
  let totalPages = 1

  const hashData = (data) => {
    return new TextEncoder()
      .encode(JSON.stringify(data))
      .reduce((hash, byte) => (hash = ((hash << 5) - hash + byte) | 0), 0)
  }

  const cachedPosts = localStorage.getItem('hintr')

  if (cachedPosts) {
    const cachedData = JSON.parse(cachedPosts)
    const cachedHash = localStorage.getItem('hintrHash')
    const newHash = hashData(cachedData)

    if (cachedHash === newHash) {
      return cachedData
    }
  }

  try {
    do {
      const response = await fetch(`${endpoint}?per_page=${perPage}&page=${page}`)
      const data = await response.json()

      if (response.ok) {
        posts = posts.concat(data.posts)
        totalPages = data.total_pages
      } else {
        break
      }

      page++
    } while (page <= totalPages)

    localStorage.setItem('hintr', JSON.stringify(posts))
    localStorage.setItem('hintrHash', hashData(posts))

    return posts

  } catch (error) {
    return null
  }
}

window.hintr.toggleSuggestions = function(e) {
  if (!window.hintr.toggleSuggestions.timer) {
    window.hintr.toggleSuggestions.timer = null
  }

  clearTimeout(window.hintr.toggleSuggestions.timer)

  let input = e.currentTarget

  window.hintr.toggleSuggestions.timer = setTimeout(() => {
    let suggestions = input.nextElementSibling
    let settings = hintrSettings
    let settingsOverride = input.getAttribute('data-hintr') ? JSON.parse(input.getAttribute('data-hintr')) : false
    let postTypes = Object.keys(settings.search_in)

    if (input.value.length > 2) {
      if (!suggestions.classList.contains('show')) {
        suggestions.classList.add('show')
      }

      if (settingsOverride) {
        postTypes = Object.keys(settingsOverride.search_in)
      }

      const cachedPosts = localStorage.getItem('hintr')

      if (cachedPosts) {
        let posts = JSON.parse(cachedPosts)

        posts = posts.filter(function (item) {
          let condition = []
          let keyword = input.value.toLowerCase()
          let title = item.title.toLowerCase()
          let metadata = item.metadata

          if (settingsOverride) {
            if (typeof settingsOverride.search_in[item.post_type] === 'undefined') return

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
        posts.forEach(item => {
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


window.hintr.hideSuggestions = function(e) {
  let input = e.currentTarget
  let list = input.nextElementSibling

  list.classList.remove('show')
}

window.hintr.eventListeners = function() {
  let inputs = document.querySelectorAll('[type="text"][data-hintr], [type="search"][data-hintr], [name=s]')

  inputs.forEach(input => {
    input.addEventListener('keyup', window.hintr.toggleSuggestions)

    document.addEventListener('click', function(e) {
      if (input !== e.target && input.nextElementSibling !== e.target) {
        input.nextElementSibling.classList.remove('show')
      }
    })
  })
}

document.addEventListener('DOMContentLoaded', function() {

  (async () => {
    const posts = await window.hintr.createLocalStorage()
    console.log('search_in:', posts)
  })()

  window.hintr.init()
  window.hintr.eventListeners()
})
