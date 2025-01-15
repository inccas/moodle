import $ from 'jquery';
import Prism from 'filter_prismjs/external/prism';

export const init = () => {
  $(document).on("filter-content-updated", function (event, nodes) {
    nodes.each(function() {
      let node = $(this)[0];
      try {
        node.querySelectorAll(':root');
        Prism.highlightAllUnder(node);
      } catch (e) {
        console.warn('PrismJS: ', node, 'not a valid node');
      }
    });
  });
};
