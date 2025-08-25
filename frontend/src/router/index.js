import Vue from 'vue'
import Router from 'vue-router'
import ExamRequestCreate from '@/views/ExamRequestCreate.vue'
import ExamRequest from '@/views/ExamRequest.vue'
import ExamRequestsList from '@/views/ExamRequestsList.vue'

Vue.use(Router)

export default new Router({
  mode: 'history',
  routes: [
    {
      path: '/',
      name: 'ExamRequestsList',
      component: ExamRequestsList
    },
    {
      path: '/exam-requests/create',
      name: 'ExamRequestCreate',
      component: ExamRequestCreate
    },
    {
      path: '/exam-requests/:id',
      name: 'ExamRequest',
      component: ExamRequest,
      props: true
    }
  ]
})
