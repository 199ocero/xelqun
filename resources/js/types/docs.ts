export type DocsLink = { label: string; url: string };

export type DocsNavigationGroup = {
    label: string;
    items: (DocsLink & { active: boolean })[];
};

export type DocsHeading = { id: string; text: string; level: number };

export type DocsBlock =
    | { type: 'html'; html: string }
    | { type: 'example' | 'flow'; name: string };

export type DocsDocument = {
    title: string;
    description: string;
    blocks: DocsBlock[];
    headings: DocsHeading[];
    canonical: string;
    group: string;
    previous: DocsLink | null;
    next: DocsLink | null;
};

export type DocsSection = {
    key: string;
    label: string;
    icon: string;
    groups: DocsNavigationGroup[];
};

export type DocsShell = {
    package: string;
    label: string;
    url: string;
    sections: DocsSection[];
};

export type DocsSearchResult = {
    title: string;
    section: string;
    package: string;
    url: string;
};
