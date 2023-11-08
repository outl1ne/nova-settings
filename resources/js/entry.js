Nova.booting((Vue, router, store) => {
  Nova.inertia('NovaSettings', require('./views/Settings').default);
  Vue.component('SettingsLoadingButton', require('./components/SettingsLoadingButton').default);
});
