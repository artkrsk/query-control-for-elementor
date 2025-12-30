/**
 * @vitest-environment jsdom
 */
import { describe, it, expect, beforeEach } from 'vitest'
import { addControlSpinner, showControlSpinner, hideControlSpinner } from '../../../src/ts/utils/spinner'

describe('spinner', () => {
  describe('addControlSpinner', () => {
    it('returns null when element is null', () => {
      expect(addControlSpinner(null)).toBeNull()
    })

    it('adds spinner after responsive switchers when present', () => {
      const el = document.createElement('div')
      el.innerHTML = '<span class="elementor-control-responsive-switchers"></span>'

      const spinner = addControlSpinner(el)

      expect(spinner).not.toBeNull()
      expect(spinner?.classList.contains('elementor-control-spinner')).toBe(true)
      expect(spinner?.style.display).toBe('none')
    })

    it('adds spinner after control title when no switchers', () => {
      const el = document.createElement('div')
      el.innerHTML = '<span class="elementor-control-title"></span>'

      const spinner = addControlSpinner(el)

      expect(spinner).not.toBeNull()
      expect(spinner?.classList.contains('elementor-control-spinner')).toBe(true)
    })

    it('returns null when neither title nor switchers exist', () => {
      const el = document.createElement('div')

      expect(addControlSpinner(el)).toBeNull()
    })

    it('prefers switchers over title when both exist', () => {
      const el = document.createElement('div')
      el.innerHTML = `
        <span class="elementor-control-title"></span>
        <span class="elementor-control-responsive-switchers"></span>
      `

      const spinner = addControlSpinner(el)
      const switcher = el.querySelector('.elementor-control-responsive-switchers')

      expect(spinner).not.toBeNull()
      expect(switcher?.nextElementSibling).toBe(spinner)
    })
  })

  describe('showControlSpinner', () => {
    it('returns null when element is null', () => {
      expect(showControlSpinner(null)).toBeNull()
    })

    it('creates and shows spinner when not exists', () => {
      const el = document.createElement('div')
      el.innerHTML = '<span class="elementor-control-title"></span>'

      const spinner = showControlSpinner(el)

      expect(spinner).not.toBeNull()
      expect(spinner?.style.display).toBe('block')
    })

    it('shows existing spinner', () => {
      const el = document.createElement('div')
      el.innerHTML = `
        <span class="elementor-control-title"></span>
        <span class="elementor-control-spinner" style="display: none;"></span>
      `

      const spinner = showControlSpinner(el)

      expect(spinner?.style.display).toBe('block')
    })

    it('returns null when spinner cannot be created', () => {
      const el = document.createElement('div')

      expect(showControlSpinner(el)).toBeNull()
    })
  })

  describe('hideControlSpinner', () => {
    it('returns null when element is null', () => {
      expect(hideControlSpinner(null)).toBeNull()
    })

    it('hides existing spinner', () => {
      const el = document.createElement('div')
      el.innerHTML = '<span class="elementor-control-spinner" style="display: block;"></span>'

      const spinner = hideControlSpinner(el)

      expect(spinner?.style.display).toBe('none')
    })

    it('returns null when spinner does not exist', () => {
      const el = document.createElement('div')

      expect(hideControlSpinner(el)).toBeNull()
    })
  })
})
