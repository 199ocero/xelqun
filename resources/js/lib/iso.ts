const COS_30 = Math.cos(Math.PI / 6);

/** A point on the floor (x, y), lifted by z, as seen from the front corner at 30°. */
export function isoPoint(x: number, y: number, z = 0): string {
    const screenX = (x - y) * COS_30;
    const screenY = (x + y) / 2 - z;

    return `${screenX.toFixed(2)},${screenY.toFixed(2)}`;
}

/** The three faces you can see of a box: its top, and the two sides facing the viewer. */
export function isoBox(
    x: number,
    y: number,
    z: number,
    width: number,
    depth: number,
    height: number,
): { top: string; left: string; right: string } {
    const x2 = x + width;
    const y2 = y + depth;
    const z2 = z + height;

    return {
        top: [
            isoPoint(x, y, z2),
            isoPoint(x2, y, z2),
            isoPoint(x2, y2, z2),
            isoPoint(x, y2, z2),
        ].join(' '),
        left: [
            isoPoint(x, y2, z2),
            isoPoint(x2, y2, z2),
            isoPoint(x2, y2, z),
            isoPoint(x, y2, z),
        ].join(' '),
        right: [
            isoPoint(x2, y, z2),
            isoPoint(x2, y2, z2),
            isoPoint(x2, y2, z),
            isoPoint(x2, y, z),
        ].join(' '),
    };
}

/**
 * Lays flat SVG onto the floor at height z, so a <rect> drawn in floor units
 * lands on a box's top face.
 */
export function isoFloor(z = 0): string {
    return `matrix(${COS_30} 0.5 ${-COS_30} 0.5 0 ${-z})`;
}
