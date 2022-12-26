export default {

    walk(vnode, callback) {
        if (!vnode) return;

        if (vnode?.component?.proxy?.field) {
            callback(vnode.component.proxy);
        } else if (vnode?.component?.subTree) {
            this.walk(vnode.component.subTree, callback);
        } else if (vnode.shapeFlag & 16) {
            const vnodes = vnode.children;
            for (let i = 0; i < vnodes.length; i++) {
                this.walk(vnodes[i], callback);
            }
        }
    }

}