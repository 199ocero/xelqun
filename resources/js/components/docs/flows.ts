/**
 * The animated diagrams of the docs, marked `<!-- flow: name -->` in a page. Each
 * step is an arrow between two lanes, or a note in one lane when both are the same.
 */
export type FlowStep = {
    from: number;
    to: number;
    label: string;
    caption: string;
};

export type Flow = {
    title: string;
    lanes: [string, string, string];
    steps: FlowStep[];
};

export const flows: Record<string, Flow> = {
    overview: {
        title: 'A form, from first visit to save',
        lanes: ['Browser', 'Laravel', 'Your PHP class'],
        steps: [
            {
                from: 0,
                to: 1,
                label: 'GET /orders/5/edit',
                caption:
                    'The browser opens a page. It’s a normal Inertia visit to your own route.',
            },
            {
                from: 1,
                to: 2,
                label: 'OrderForm::make($order)',
                caption:
                    'Your controller makes the form, and it fills itself from the record.',
            },
            {
                from: 2,
                to: 2,
                label: 'toSchema()',
                caption:
                    'The form turns itself into JSON, and adds an encrypted token that names its class.',
            },
            {
                from: 2,
                to: 0,
                label: 'schema + token',
                caption:
                    'Inertia sends the JSON as a prop. Vue draws one component for each node in it.',
            },
            {
                from: 0,
                to: 0,
                label: 'country = PH',
                caption:
                    'The user picks a country. The state field depends on it, so the server must hear about it.',
            },
            {
                from: 0,
                to: 1,
                label: 'POST /_xelqun/forms/update',
                caption:
                    'The browser posts the token and the values. Nothing is kept on the server between requests.',
            },
            {
                from: 1,
                to: 2,
                label: 'decrypt · rebuild · authorize',
                caption:
                    'The server decrypts the token, builds your class again from scratch, and checks permissions.',
            },
            {
                from: 2,
                to: 0,
                label: 'patch: new options',
                caption:
                    'Your options() closure runs on the server. Only the field that changed is sent back.',
            },
            {
                from: 0,
                to: 1,
                label: 'PUT /orders/5',
                caption:
                    'Saving is an Inertia visit to your own route, with the form’s values.',
            },
            {
                from: 1,
                to: 2,
                label: '$form->save($request)',
                caption:
                    'The server locks, validates and saves in a transaction. The browser is never trusted.',
            },
        ],
    },

    'forms-live': {
        title: 'A live update',
        lanes: ['Field (Vue)', 'Form store', 'Server'],
        steps: [
            {
                from: 0,
                to: 1,
                label: 'store.set("data.country_id")',
                caption:
                    'The user picks a country. useField() writes the value into the store.',
            },
            {
                from: 1,
                to: 1,
                label: 'live? in deps?',
                caption:
                    'The store checks whether the field is live, or whether another field’s deps include it.',
            },
            {
                from: 1,
                to: 1,
                label: 'wait, then batch',
                caption:
                    'The queue waits for the change, blur or debounce, and cancels any request still running.',
            },
            {
                from: 1,
                to: 2,
                label: '{ token, seq: 7, state, changed }',
                caption:
                    'It posts the token, a sequence number, all the values, and what changed.',
            },
            {
                from: 2,
                to: 2,
                label: 'resolveForm(token)',
                caption:
                    'The server decrypts the token, checks the user, loads the record and calls authorize().',
            },
            {
                from: 2,
                to: 2,
                label: 'prepare() · locks',
                caption:
                    'Posted values are loaded, and locked fields get their server values back.',
            },
            {
                from: 2,
                to: 2,
                label: 'afterStateUpdated · closures',
                caption:
                    'Hooks for the changed field run, then every node whose deps changed is serialized again.',
            },
            {
                from: 2,
                to: 1,
                label: '{ seq: 7, set, schema, errors }',
                caption:
                    'The patch has only the nodes and values that changed.',
            },
            {
                from: 1,
                to: 1,
                label: 'seq is latest?',
                caption:
                    'A late answer to an older request is dropped, and values typed since are kept.',
            },
            {
                from: 1,
                to: 0,
                label: 'new options',
                caption:
                    'The state field updates in place. Nothing re-mounts, and focus stays where it was.',
            },
        ],
    },

    'tables-query': {
        title: 'Sorting a table',
        lanes: ['XqTable', 'QueryController', 'Your table'],
        steps: [
            {
                from: 0,
                to: 0,
                label: 'click “Price”',
                caption:
                    'The user clicks a column header. The store merges the new sort into the last query.',
            },
            {
                from: 0,
                to: 1,
                label: 'POST /_xelqun/tables/query',
                caption:
                    'It posts the token and the query. A newer request would cancel this one.',
            },
            {
                from: 1,
                to: 2,
                label: 'resolveTable(token)',
                caption:
                    'The server rebuilds your table from its class and context, and checks authorize().',
            },
            {
                from: 2,
                to: 2,
                label: 'TableState::from()',
                caption:
                    'The query is cleaned up. A sort on a column that isn’t sortable is dropped.',
            },
            {
                from: 2,
                to: 2,
                label: 'query() + search + filters + sort',
                caption:
                    'buildQuery() starts from your query(), then adds eager loads, search, filters and the sort.',
            },
            {
                from: 2,
                to: 2,
                label: 'toCell() for each column',
                caption:
                    'Each record becomes a row, and each column turns its value into a cell.',
            },
            {
                from: 2,
                to: 0,
                label: '{ rows, pagination, query }',
                caption:
                    'toResult() sends the rows back. It’s the same method the first page load used.',
            },
            {
                from: 0,
                to: 0,
                label: 'router.replace(?products[sort]=price)',
                caption:
                    'The rows are replaced, and the URL is updated so Back and shared links work.',
            },
        ],
    },

    'panels-edit': {
        title: 'Opening and saving an edit page',
        lanes: ['Browser', 'Panel', 'Forms'],
        steps: [
            {
                from: 0,
                to: 1,
                label: 'GET /admin/products/5/edit',
                caption:
                    'The route points straight at the EditRecord page class, with the resource as a route default.',
            },
            {
                from: 1,
                to: 1,
                label: 'EnterPanel · Authenticate',
                caption:
                    'Middleware enters the panel, and checks the user is signed in and may enter it.',
            },
            {
                from: 1,
                to: 1,
                label: 'findRecord(5) · can("update")',
                caption:
                    'The page finds the record through the resource’s query, and asks your policy.',
            },
            {
                from: 1,
                to: 2,
                label: 'ResourceForm::for(...)',
                caption:
                    'The page builds a ResourceForm. Its schema() is your resource’s form().',
            },
            {
                from: 2,
                to: 1,
                label: 'toSchema()',
                caption:
                    'The forms package turns it into JSON with a token, stamped with the panel and tenant.',
            },
            {
                from: 1,
                to: 0,
                label: "render('xelqun/Panel')",
                caption:
                    'Inertia sends the page content, plus the shell: navigation, theme and user.',
            },
            {
                from: 0,
                to: 0,
                label: '<XqForm :form="schema">',
                caption:
                    'XqPanelPage draws the shell, and hands the form’s schema to XqForm.',
            },
            {
                from: 0,
                to: 1,
                label: 'PUT /admin/products/5',
                caption:
                    'The user saves. The form sends an Inertia visit to the resource’s update route.',
            },
            {
                from: 1,
                to: 2,
                label: 'EditRecord::update() → save()',
                caption:
                    'The page finds the record and checks the policy again, then calls the forms package’s save().',
            },
            {
                from: 2,
                to: 0,
                label: 'redirect + “Saved”',
                caption:
                    'The record is saved in a transaction, and the user is redirected with a notification.',
            },
        ],
    },
};
