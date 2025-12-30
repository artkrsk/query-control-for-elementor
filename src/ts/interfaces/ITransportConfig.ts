import type { TQueryData } from '../types'

/** Configuration for Select2 AJAX transport */
export interface ITransportConfig {
  queryData?: TQueryData | (() => TQueryData) | undefined
  before?: (() => void) | undefined
  after?: (() => void) | undefined
}
