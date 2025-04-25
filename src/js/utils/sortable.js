/**
 * Gets required DOM elements for sortable functionality
 *
 * @param {Object} $el - The jQuery element container (control wrapper)
 * @returns {Object|null} - Object containing required DOM elements or null if not found
 */
const getSortableElements = ($el) => {
  if (!$el || !$el.length) {
    return null
  }

  const $select = $el.find('select')
  const $list = $select.next().find('ul')

  if (!$select.length || !$list.length) {
    return null
  }

  return {
    $select,
    $list,
    $items: $list.find('li')
  }
}

/**
 * Syncs the select options order with the visual sortable list
 *
 * @param {Object} $select - The jQuery select element
 * @param {Object} $item - The jQuery sortable item being reordered
 *
 * @returns {boolean} - True if sync was successful, false otherwise
 */
const syncSelectWithSortableList = ($select, $item) => {
  const $listItems = $item.parent().children('[title]')

  if (!$listItems.length) {
    return false
  }

  $listItems.each(function () {
    const title = jQuery(this).attr('title')
    if (!title) return

    // Find the exact matching option by text content
    const $matchingOption = $select.find('option').filter(function () {
      return jQuery(this).text() === title
    })

    if ($matchingOption.length) {
      // Move the option to the end to match the visual order
      $matchingOption.detach()
      $select.append($matchingOption)
    }
  })

  // Trigger change event to notify Elementor of the update
  $select.trigger('change')

  return true
}

/**
 * Rearranges sortable elements based on IDs and options
 *
 * Takes the sorted post IDs and rearranges the visual list items
 * to match the order specified in the sorted array.
 *
 * @param {Object} $el - The jQuery element container (control wrapper)
 * @param {Object} options - Options for rearranging
 * @param {Object} options.allPosts - Object mapping post IDs to titles
 * @param {Array} options.sortedPosts - Array of post IDs in sorted order
 *
 * @returns {boolean} - True if rearrangement was successful, false otherwise
 */
export const rearrangeSortable = ($el, options) => {
  const { allPosts, sortedPosts = [] } = options

  if (!allPosts || !sortedPosts.length) {
    return false
  }

  const elements = getSortableElements($el)
  if (!elements) {
    return false
  }

  const { $list, $items } = elements
  const lastItem = $items.eq(-1)

  // Move items according to the sortedPosts order
  sortedPosts.forEach((id) => {
    const title = allPosts[id]
    if (title) {
      const $item = $list.find(`li[title="${title}"]`)
      if ($item.length) {
        $item.insertBefore(lastItem)
      }
    }
  })

  return true
}

/**
 * Attaches sortable functionality to a list
 *
 * Initializes jQuery UI sortable on the list and sets up event handlers
 * to update the select element when items are reordered.
 *
 * @param {Object} $el - The jQuery element container (control wrapper)
 *
 * @returns {boolean} - True if sortable was attached, false otherwise
 */
export const attachSortable = ($el) => {
  const elements = getSortableElements($el)
  if (!elements) {
    return false
  }

  const { $select, $list } = elements

  // Check if jQuery UI sortable is available
  if (typeof $list.sortable !== 'function') {
    console.warn('jQuery UI is not loaded. Sortable functionality cannot be attached.')
    return false
  }

  // Initialize sortable
  $list.sortable({
    containment: 'parent',
    stop: function (_event, ui) {
      syncSelectWithSortableList($select, ui.item)
    }
  })

  return true
}
