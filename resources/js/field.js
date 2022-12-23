import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-eom-depend-fill', IndexField)
  app.component('detail-eom-depend-fill', DetailField)
  app.component('form-eom-depend-fill', FormField)
})
