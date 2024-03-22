'use strict';
async function post(path, params = null) {
    async function post_async(path, params) {
        if (params) {
            const form_data = new FormData();
            for (const [name, value] of Object.entries(params))
            {
                form_data.append(name, value);
            }
            return await fetch(path, {
                method: 'POST',
                body: form_data
            });
        }
        return await fetch(path, {
            method: 'POST'
        });
    }
    try {
        const response = await post_async(path, params);
        if (!response.ok) {
            return null;
        }
        const contentType = response.headers.get("content-type");
        if (!contentType || contentType.indexOf("application/json") === -1) {
            console.log(await response.text());
            return null;
        }
        return await response.json();
    } catch (err) {
        console.warn(err);
        return null;
    }
}
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}