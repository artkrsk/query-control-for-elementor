import { registerQueryPostsSelect } from './QueryPostsSelect'
import { registerQueryTermsSelect } from './QueryTermsSelect'
import { registerQueryPostTypesSelect } from './QueryPostTypesSelect'
import { registerQueryMenusSelect } from './QueryMenusSelect'

export * from './QueryPostsSelect'
export * from './QueryTermsSelect'
export * from './QueryPostTypesSelect'
export * from './QueryMenusSelect'

/** Registers all query controls with Elementor */
export const registerAllControls = (): void => {
  registerQueryPostsSelect()
  registerQueryTermsSelect()
  registerQueryPostTypesSelect()
  registerQueryMenusSelect()
}
