/**
 * Generates the HTML markup for a spinner element
 *
 * @private
 * @returns {string} HTML string for the spinner element
 */
const getSpinnerHTML = () => {
  return `<span class="elementor-control-spinner" style="display: none;">&nbsp;<i class="eicon-spinner eicon-animation-spin"></i>&nbsp;</span>`
}

/**
 * Adds a spinner element to a control
 *
 * @param {HTMLElement} el - The control element to add the spinner to
 * @returns {HTMLElement|null} The created spinner element or null if parent element is invalid
 */
export const addControlSpinner = (el) => {
  if (!el) {
    return null
  }

  const titleEl = el.querySelector('.elementor-control-title')
  const switcherEl = el.querySelector('.elementor-control-responsive-switchers')
  const spinnerMarkup = getSpinnerHTML()

  if (switcherEl) {
    switcherEl.insertAdjacentHTML('afterend', spinnerMarkup)
    // Cast Element to HTMLElement
    const spinnerEl = /** @type {HTMLElement} */ (switcherEl.nextElementSibling)
    return spinnerEl
  } else if (titleEl) {
    titleEl.insertAdjacentHTML('afterend', spinnerMarkup)
    const spinnerEl = /** @type {HTMLElement} */ (titleEl.nextElementSibling)
    return spinnerEl
  }

  return null
}

/**
 * Shows the spinner for a control, creating it if needed
 *
 * @param {HTMLElement} el - The control element containing the spinner
 * @returns {HTMLElement|null} The spinner element or null if parent element is invalid
 */
export const showControlSpinner = (el) => {
  if (!el) {
    return null
  }

  let spinnerEl = /** @type {HTMLElement|null} */ (el.querySelector('.elementor-control-spinner'))

  if (!spinnerEl) {
    spinnerEl = addControlSpinner(el)
  }

  if (spinnerEl) {
    spinnerEl.style.display = 'block'
  }

  return spinnerEl
}

/**
 * Hides the spinner for a control
 *
 * @param {HTMLElement} el - The control element containing the spinner
 * @returns {HTMLElement|null} The spinner element or null if parent element is invalid
 */
export const hideControlSpinner = (el) => {
  if (!el) {
    return null
  }

  const spinnerEl = /** @type {HTMLElement|null} */ (el.querySelector('.elementor-control-spinner'))

  if (spinnerEl) {
    spinnerEl.style.display = 'none'
  }

  return spinnerEl
}
