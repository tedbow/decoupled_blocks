System.config({
  //use typescript for compilation
  transpiler: 'typescript',
  //typescript compiler options
  typescriptOptions: {
    emitDecoratorMetadata: true
  },
  //map tells the System loader where to look for things
  map: {
    app: '/modules/pdb/modules/pdb_ng2/assets/app'
  },
  //packages defines our app package
  packages: {
    app: {
      main: 'app',
      defaultExtension: 'ts'
    },
    angular: { defaultExtension: false }
  }
});
System.import('app').catch(console.error.bind(console));