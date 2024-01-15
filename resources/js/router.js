import Vue from 'vue';
import Router from 'vue-router';
import guest from './services/middleware/guest';
import auth from './services/middleware/auth';

Vue.use(Router);

const routes = [
  {
    path: '/',
    beforeEnter: (to, from, next) => {
      window.location = `${process.env.MIX_APP_URL}/home`;
    }
  },
  {
    path: '/help',
    beforeEnter: (to, from, next) => {
      window.location = `${process.env.MIX_APP_URL}/help`;
    }
  },
  {
    path: '/loader',
    name: 'loader',
    component: () => import('@/views/pages/loader')
  },
  {
    path: '/login',
    name: 'login',
    beforeEnter: guest,
    component: () => import('@/views/pages/auth/login')
  },
  {
    path: '/live/:code',
    name: 'live',
    component: () => import('@/views/pages/live')
  },
  {
    path: '/password/reset/:token',
    beforeEnter: guest,
    component: () => import('@/views/pages/auth/reset')
  },
  {
    path: '/privacy',
    name: 'privacy',
    component: () => import('@/views/pages/privacy')
  },
  {
    path: '/terms',
    name: 'terms',
    component: () => import('@/views/pages/terms')
  },
  {
    path: '/redeem/:coupon',
    name: 'redeem-coupon',
    beforeEnter: guest,
    component: () => import('@/views/pages/redeem')
  },
  {
    path: '/appsumo/:id',
    name: 'appsumo',
    beforeEnter: guest,
    component: () => import('@/views/pages/appsumo')
  },
  {
    path: '/redeem',
    name: 'redeem',
    beforeEnter: guest,
    component: () => import('@/views/pages/redeem')
  },
  {
    path: '/app',
    redirect: '/app/home',
    beforeEnter: auth,
    component: () => import('@/views/layout/layout'),
    children: [
      {
        path: 'home',
        name: 'home',
        component: () => import('@/views/pages/home')
      },
      {
        path: 'vidgen',
        name: 'vidgen',
        component: () => import('@/views/pages/vidpopups')
      },
      {
        path: 'vidgen-list',
        name: 'vidgen-list',
        component: () => import('@/views/pages/vidpopup-list')
      },
      {
        path: 'ai-video',
        name: 'ai-video',
        component: () => import('@/views/pages/ai/ai-layout')
      },
      {
        path: 'ai-list',
        name: 'ai-list',
        component: () => import('@/views/pages/ai/ai-list')
      },
      {
        path: 'vidgen/create',
        name: 'vidgen-creator',
        component: () => import('@/views/pages/vidpopup/vidpopups-creator')
      },
      {
        path: 'vidgen/:id/:step/manage',
        name: 'vidpopup',
        component: () => import('@/views/pages/vidpopup/vidpopup-manage')
      },
      {
        path: 'vidgen/:id/:step/upload',
        name: 'vidpopup-upload',
        component: () => import('@/views/pages/vidpopup/vidpopup-upload')
      },
      {
        path: 'vidgen/:id/:step/camera',
        name: 'vidpopup-camera',
        component: () => import('@/views/pages/vidpopup/vidpopup-camera')
      },
      {
        path: 'vidgen/:id/:step/library',
        name: 'vidpopup-library',
        component: () => import('@/views/pages/vidpopup/vidpopup-library')
      },
      {
        path: 'vidgen/:id/:step/ai',
        name: 'vidpopup-ai',
        component: () => import('@/views/pages/vidpopup/vidpopup-ai')
      },
      {
        path: 'vidgen/:id/:step/screen',
        name: 'vidpopup-screen',
        component: () => import('@/views/pages/vidpopup/vidpopup-screen')
      },
      {
        path: 'vidgen/:id/end-screen',
        name: 'vidpopup-endscreen',
        component: () => import('@/views/pages/vidpopup/vidpopup-endscreen')
      },
      {
        path: 'vidgen/:id/:step/edit',
        name: 'vidpopup-edit',
        beforeEnter: auth,
        component: () => import('@/views/pages/vidpopup/steps/step-layout'),
        children: [
          {
            path: 'overlay',
            name: 'step-overlay',
            component: () => import('@/views/pages/vidpopup/steps/step-overlay')
          },
          {
            path: 'answer',
            name: 'step-answer',
            component: () => import('@/views/pages/vidpopup/steps/step-answer')
          },
          {
            path: 'video',
            name: 'step-video',
            component: () => import('@/views/pages/vidpopup/steps/step-video')
          },
          {
            path: 'logic',
            name: 'step-logic',
            component: () => import('@/views/pages/vidpopup/steps/step-logic')
          }
        ]
      },
      {
        path: 'vidgen/:id/settings',
        name: 'vidpopup-layout',
        beforeEnter: auth,
        component: () =>
          import('@/views/pages/vidpopup/settings/vidpopup-layout'),
        children: [
          {
            path: 'settings',
            name: 'vidpopup-settings',
            component: () =>
              import('@/views/pages/vidpopup/settings/vidpopup-settings')
          },
          {
            path: 'advance',
            name: 'vidpopup-advance',
            component: () =>
              import('@/views/pages/vidpopup/settings/vidpopup-advance')
          },
          {
            path: 'link',
            name: 'vidpopup-link',
            component: () =>
              import('@/views/pages/vidpopup/settings/vidpopup-link')
          },
          {
            path: 'widget',
            name: 'vidpopup-widget',
            component: () =>
              import('@/views/pages/vidpopup/settings/vidpopup-widget')
          },
          {
            path: 'embed',
            name: 'vidpopup-embed',
            component: () =>
              import('@/views/pages/vidpopup/settings/vidpopup-embed')
          }
        ]
      },
      {
        path: 'replies',
        name: 'replies',
        component: () => import('@/views/pages/replies')
      },
      {
        path: 'settings',
        name: 'settings',
        component: () => import('@/views/pages/settings')
      },
      {
        path: 'integrations',
        name: 'integrations',
        component: () => import('@/views/pages/integrations')
      },
      {
        path: 'templates',
        name: 'templates',
        component: () => import('@/views/pages/templates')
      },
      {
        path: 'statistics',
        name: 'statistics',
        component: () => import('@/views/pages/statistics')
      },
      {
        path: 'requests',
        name: 'requests',
        component: () => import('@/views/pages/requests')
      },
      {
        path: 'responses',
        name: 'responses',
        component: () => import('@/views/pages/responses')
      },
      {
        path: 'transaction',
        name: 'transaction',
        component: () => import('@/views/pages/transaction')
      },
      {
        path: 'admin-payout',
        name: 'admin-payout',
        component: () => import('@/views/pages/admin/payout')
      },
      {
        path: 'admin-pay-publisher',
        name: 'admin-pay-publisher',
        component: () => import('@/views/pages/admin/pay')
      },
      {
        path: 'payout',
        name: 'payout',
        component: () => import('@/views/pages/payout')
      },
      {
        path: 'gen-coupon',
        name: 'gen-coupon',
        component: () => import('@/views/pages/coupons')
      },
      {
        path: 'trainings',
        name: 'trainings',
        component: () => import('@/views/pages/trainings')
      },
      {
        path: 'memberships',
        name: 'memberships',
        component: () => import('@/views/pages/memberships')
      },
      {
        path: 'members',
        name: 'members',
        component: () => import('@/views/pages/members')
      },

      {
        path: 'supports',
        name: 'supports',
        component: () => import('@/views/pages/supports')
      }
    ]
  },
  {
    path: '/error',
    name: 'error',
    component: () => import('@/views/pages/error')
  },
  {
    path: '*',
    component: () => import('@/views/pages/error')
  }
];

const router = new Router({
  mode: 'history',
  linkActiveClass: 'open',
  routes,
  scrollBehavior() {
    return { x: 0, y: 0 };
  }
});

export default router;
