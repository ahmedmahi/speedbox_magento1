/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Speedbox
 * @author      Speedbox ( http://www.speedbox.ma)
 * @developer   Ahmed MAHI <1hmedmahi@gmail.com> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
document.observe("dom:loaded", function() {
    $$('.order-comments .order-about dd').each(function(e) {
        e.replace(htmlDecode(e.innerHTML));
    });
});

function htmlDecode(input) {
    var d = document.createElement('div');
    d.innerHTML = input;
    return d.childNodes[0].nodeValue;
}