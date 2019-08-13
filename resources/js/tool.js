Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'nova-settings',
      path: '/nova-settings',
      component: require('./views/Settings'),
    },
  ]);
});
