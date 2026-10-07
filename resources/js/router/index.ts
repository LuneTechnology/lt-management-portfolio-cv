import { createRouter, createWebHistory } from 'vue-router'
import { useAuth } from '@/composables/useAuth'

const router = createRouter({
  history: createWebHistory(),
  scrollBehavior(to, from, savedPosition) {
    return savedPosition || { left: 0, top: 0 }
  },
  routes: [
    {
      path: '/',
      name: 'Ecommerce',
      component: () => import('../views/Ecommerce.vue'),
      meta: {
        requiresAuth: true,
        title: 'eCommerce Dashboard',
      },
    },
    {
      path: '/products',
      name: 'Products',
      component: () => import('../views/Products/Products.vue'),
    },
    {
      path: '/users',
      name: 'Users',
      component: () =>
        import('../views/Management/User/User.vue'),
      meta: {
        requiresAuth: true,
        superAdminOnly: true,
      },
    },
    {
      path: '/calendar',
      name: 'Calendar',
      component: () => import('../views/Others/Calendar.vue'),
      meta: {
        title: 'Calendar',
      },
    },
    {
      path: '/profile',
      name: 'Profile',
      component: () => import('../views/Others/UserProfile.vue'),
      meta: {
        title: 'Profile',
      },
    },
    {
      path: '/form-elements',
      name: 'Form Elements',
      component: () => import('../views/Forms/FormElements.vue'),
      meta: {
        title: 'Form Elements',
      },
    },
    {
      path: '/basic-tables',
      name: 'Basic Tables',
      component: () => import('../views/Tables/BasicTables.vue'),
      meta: {
        title: 'Basic Tables',
      },
    },
    {
      path: '/line-chart',
      name: 'Line Chart',
      component: () => import('../views/Chart/LineChart/LineChart.vue'),
    },
    {
      path: '/bar-chart',
      name: 'Bar Chart',
      component: () => import('../views/Chart/BarChart/BarChart.vue'),
    },
    {
      path: '/alerts',
      name: 'Alerts',
      component: () => import('../views/UiElements/Alerts.vue'),
      meta: {
        title: 'Alerts',
      },
    },
    {
      path: '/avatars',
      name: 'Avatars',
      component: () => import('../views/UiElements/Avatars.vue'),
      meta: {
        title: 'Avatars',
      },
    },
    {
      path: '/badge',
      name: 'Badge',
      component: () => import('../views/UiElements/Badges.vue'),
      meta: {
        title: 'Badge',
      },
    },
    {
      path: '/buttons',
      name: 'Buttons',
      component: () => import('../views/UiElements/Buttons.vue'),
      meta: {
        title: 'Buttons',
      },
    },
    {
      path: '/images',
      name: 'Images',
      component: () => import('../views/UiElements/Images.vue'),
      meta: {
        title: 'Images',
      },
    },
    {
      path: '/videos',
      name: 'Videos',
      component: () => import('../views/UiElements/Videos.vue'),
      meta: {
        title: 'Videos',
      },
    },
    {
      path: '/blank',
      name: 'Blank',
      component: () => import('../views/Pages/BlankPage.vue'),
      meta: {
        title: 'Blank',
      },
    },
    {
      path: '/error-404',
      name: '404 Error',
      component: () => import('../views/Errors/FourZeroFour.vue'),
      meta: {
        title: '404 Error',
      },
    },
    {
      path: '/signin',
      name: 'Signin',
      component: () => import('../views/Auth/Signin.vue'),
      meta: {
        title: 'Signin',
      },
    },
    {
      path: '/signup',
      name: 'Signup',
      component: () => import('../views/Auth/Signup.vue'),
      meta: {
        title: 'Signup',
      },
    },
    {
      path: '/stack',
      name: 'Stack',
      component: () => import('../views/Management/Stack/Stack.vue'),
      meta: {
        requiresAuth: true,
        title: 'Stack Management',
      },
    },
    {
      path: '/category',
      name: 'Category',
      component: () => import('../views/Management/Category/Category.vue'),
      meta: {
        requiresAuth: true,
        title: 'Category Management',
      },
    },
    {
      path: '/tags',
      name: 'Tags',
      component: () => import('../views/Management/Tag/Tag.vue'),
      meta: {
        requiresAuth: true,
        title: 'Tag Management',
      },
    },
    {
      path: '/experiences',
      name: 'Experience',
      component: () => import('../views/Management/Experience/Experience.vue'),
      meta: {
        requiresAuth: true,
        title: 'Experience Management',
      },
    },
    {
      path: '/Works',
      name: 'Work',
      component: () => import('../views/Management/Work/Work.vue'),
      meta: {
        requiresAuth: true,
        title: 'Work Management',
      },
    },
    {
      path: '/experience-stacks',
      name: 'Experience Stack',
      component: () => import('../views/Management/ExperienceStack/ExperienceStack.vue'),
      meta: {
        requiresAuth: true,
        title: 'Experience Stack Management',
      },
    },
    {
      path: '/position-types',
      name: 'Position Types',
      component: () => import('../views/Management/PositionType/PositionType.vue'),
      meta: {
        requiresAuth: true,
        title: 'Position Type Management',
      },
    },
    {
      path: '/work-types',
      name: 'WorkTypes',
      component: () => import('../views/Management/WorkType/WorkType.vue'),
      meta: {
        requiresAuth: true,
        title: 'Work Type Management',
      },
    },
    {
      path: '/tasks',
      name: 'Tasks',
      component: () => import('../views/Management/Task/Task.vue'),
      meta: {
        requiresAuth: true,
        title: 'Task Management',
      },
    },
    {
      path: '/educations',
      name: 'Education',
      component: () => import('../views/Management/Education/Education.vue'),
      meta: {
        requiresAuth: true,
        title: 'Education Management',
      },
    },
    {
  path: '/achievements',
  name: 'Achievement',
  component: () => import('../views/Management/Achievement/Achievement.vue'),
  meta: {
    title: 'Achievement Management',
  },
},
  ],
})

export default router

router.beforeEach(async (to, from) => {
  const {
    user,
    initialized,
    fetchUser,
  } = useAuth()

  if (!initialized.value) {
    await fetchUser()
  }

  // Belum login
  if (
    to.meta.requiresAuth &&
    !user.value
  ) {
    return {
      name: 'Signin',
    }
  }

  // Sudah login tapi halaman khusus Super Admin
  if (
    to.meta.superAdminOnly &&
    user.value?.role?.name !== 'Super Admin'
  ) {
    return {
      name: 'Ecommerce',
    }
  }

  // Sudah login dan mencoba buka signin
  if (
    to.name === 'Signin' &&
    user.value
  ) {
    return {
      name: 'Ecommerce',
    }
  }
})