import type { TSelectOptions } from '../types'

/** Options for rearranging sortable elements */
export interface IRearrangeOptions {
  allPosts: TSelectOptions
  sortedPosts: (string | number)[]
}
