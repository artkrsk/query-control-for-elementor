import { register as registerQueryPostTypesSelect } from './controls/QueryPostTypesSelect'
import { register as registerQueryPostsSelect } from './controls/QueryPostsSelect'
import { register as registerQueryTermsSelect } from './controls/QueryTermsSelect'
import { register as registerQueryMenusSelect } from './controls/QueryMenusSelect'

// Register all query controls when Elementor editor is initialized
window.addEventListener('elementor/init', () => {
  registerQueryPostTypesSelect()
  registerQueryPostsSelect()
  registerQueryTermsSelect()
  registerQueryMenusSelect()
})
