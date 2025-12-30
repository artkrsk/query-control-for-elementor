/**
 * Configuration for loadObjectsElementor AJAX wrapper
 */
export interface ILoadObjectsConfig {
  /** IDs to load */
  ids?: (string | number)[]

  /** Additional data for the request */
  data?: Record<string, unknown>

  /** Callback before request */
  before?: () => void

  /** Callback on success */
  onSuccess?: (response: Record<string, string>) => Promise<void> | void

  /** Callback on error */
  onError?: (error: unknown) => Promise<void> | void
}
