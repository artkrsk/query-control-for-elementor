/**
 * Configuration for fetchElementor AJAX wrapper
 */
export interface IFetchConfig {
  /** Data to send with the request */
  data?: Record<string, unknown> | undefined

  /** Callback before request */
  before?: (() => void) | undefined

  /** Callback after request (success or error) */
  after?: ((response?: unknown) => Promise<void> | void) | undefined

  /** Callback on success */
  onSuccess?: ((response: unknown) => Promise<void> | void) | undefined

  /** Callback on error */
  onError?: ((error: unknown) => Promise<void> | void) | undefined
}
