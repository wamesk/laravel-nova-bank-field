import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-bank', IndexField)
  app.component('detail-bank', DetailField)
  app.component('form-bank', FormField)
})
