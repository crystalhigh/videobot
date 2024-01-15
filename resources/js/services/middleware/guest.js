import store from '@/services/store';

export default (to, from, next) => {
  if (store.getters.check) {
    if (store.getters.currentUser.role == 'admin')
      next({ name: 'admin-payout' });
    else
      next({ name: 'statistics' });
  } else {
    next();
  }
};
