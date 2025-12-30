/** Generates the HTML markup for a spinner element */
const getSpinnerHTML = (): string => {
  return `<span class="elementor-control-spinner" style="display: none;">&nbsp;<i class="eicon-spinner eicon-animation-spin"></i>&nbsp;</span>`
}

/**
 * Adds a spinner element to a control
 *
 * @param el - The control element to add the spinner to
 * @returns The created spinner element or null if parent element is invalid
 */
export const addControlSpinner = (el: HTMLElement | null): HTMLElement | null => {
  if (!el) {
    return null
  }

  const titleEl = el.querySelector('.elementor-control-title')
  const switcherEl = el.querySelector('.elementor-control-responsive-switchers')
  const spinnerMarkup = getSpinnerHTML()

  if (switcherEl) {
    switcherEl.insertAdjacentHTML('afterend', spinnerMarkup)
    return switcherEl.nextElementSibling as HTMLElement
  } else if (titleEl) {
    titleEl.insertAdjacentHTML('afterend', spinnerMarkup)
    return titleEl.nextElementSibling as HTMLElement
  }

  return null
}

/**
 * Shows the spinner for a control, creating it if needed
 *
 * @param el - The control element containing the spinner
 * @returns The spinner element or null if parent element is invalid
 */
export const showControlSpinner = (el: HTMLElement | null): HTMLElement | null => {
  if (!el) {
    return null
  }

  let spinnerEl = el.querySelector('.elementor-control-spinner') as HTMLElement | null

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
 * @param el - The control element containing the spinner
 * @returns The spinner element or null if parent element is invalid
 */
export const hideControlSpinner = (el: HTMLElement | null): HTMLElement | null => {
  if (!el) {
    return null
  }

  const spinnerEl = el.querySelector('.elementor-control-spinner') as HTMLElement | null

  if (spinnerEl) {
    spinnerEl.style.display = 'none'
  }

  return spinnerEl
}
