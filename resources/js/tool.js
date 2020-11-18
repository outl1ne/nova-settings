Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'nova-settings',
      path: '/nova-settings/:id?',
      component: require('./views/Settings').default,
    },
  ]);
});
