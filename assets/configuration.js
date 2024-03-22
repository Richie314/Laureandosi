if (!('CKSource' in window)) {
    throw Error('CKEditor missing!');
}
//Watchdog setup
const watchdog = new CKSource.EditorWatchdog();

window.watchdog = watchdog;
watchdog.setCreator((element, config) => CKSource.Editor
    .create(element, config));

watchdog.setDestructor(editor => editor.destroy());

watchdog.on('error', handleWatchDogError);

function handleWatchDogError(error) {
    console.error('Qualcosa è anadato storto');
    console.error(error);
}
watchdog.create(elem, CkEditorConfig)
    .then(editor => editor)
    .catch(err => {
        console.error(err);
    });