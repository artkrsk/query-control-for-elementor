import type { ISortableElements, IRearrangeOptions } from '../interfaces'

/**
 * Gets required DOM elements for sortable functionality
 *
 * @param $el - The jQuery element container (control wrapper)
 * @returns Object containing required DOM elements or null if not found
 */
const getSortableElements = ($el: JQuery | null): ISortableElements | null => {
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
 * @param $select - The jQuery select element
 * @param $item - The jQuery sortable item being reordered
 * @returns True if sync was successful, false otherwise
 */
const syncSelectWithSortableList = ($select: JQuery, $item: JQuery): boolean => {
  const $listItems = $item.parent().children('[title]')

  if (!$listItems.length) {
    return false
  }

  $listItems.each(function (this: HTMLElement) {
    const title = jQuery(this).attr('title')
    if (!title) {
      return
    }

    const $matchingOption = $select.find('option').filter(function (this: HTMLElement) {
      return jQuery(this).text() === title
    })

    if ($matchingOption.length) {
      $matchingOption.detach()
      $select.append($matchingOption)
    }
  })

  $select.trigger('change')

  return true
}

/**
 * Rearranges sortable elements based on IDs and options
 *
 * Takes the sorted post IDs and rearranges the visual list items
 * to match the order specified in the sorted array.
 *
 * @param $el - The jQuery element container (control wrapper)
 * @param options - Options for rearranging
 * @returns True if rearrangement was successful, false otherwise
 */
export const rearrangeSortable = ($el: JQuery, options: IRearrangeOptions): boolean => {
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
 * @param $el - The jQuery element container (control wrapper)
 * @returns True if sortable was attached, false otherwise
 */
export const attachSortable = ($el: JQuery): boolean => {
  const elements = getSortableElements($el)
  if (!elements) {
    return false
  }

  const { $select, $list } = elements

  if (typeof ($list as JQuery & { sortable?: unknown }).sortable !== 'function') {
    console.warn('jQuery UI is not loaded. Sortable functionality cannot be attached.')
    return false
  }

  ;($list as JQuery & { sortable: (options: Record<string, unknown>) => void }).sortable({
    containment: 'parent',
    stop: function (_event: unknown, ui: { item: JQuery }) {
      syncSelectWithSortableList($select, ui.item)
    }
  })

  return true
}
