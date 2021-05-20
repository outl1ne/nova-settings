Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'nova-settings',
      path: `/${Nova.config?.novaSettings?.basePath ?? 'nova-settings'}/:id?`,
      component: require('./views/Settings').default,
    },
  ]);
});
