<?php 



/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Sda\Component\Sdajem\Administrator\Model\FittingModel;

/** @var \Sda\Component\Sdajem\Site\View\Event\HtmlView $this */

$wa=$this->getDocument()->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_sdajem');
$wa->useScript('com_sdajem.plan');
$wa->useStyle('com_sdajem.sdajem');
$wa->useStyle('com_sdajem.planing');
$wa->getRegistry()->addExtensionRegistryFile('vendor');
$wa->useScript('jquery');
$tparams = $this->item->paramsRegistry;

$boxX = $tparams->get('sda_planing_x');
$boxY = $tparams->get('sda_planing_y');

$event = $this->item;
?>

<div id="planingTool">
	<div class="well">

        <div class="buttonContainer">
			<button id="toSvg" class="btn me-2 btn-primary col-auto"><?php echo Text::_('COM_SDAJEM_TO_SVG') ?></button>
			<button id="save" class="btn me-2 btn-primary col-auto"><?php echo Text::_('COM_SDAJEM_PLANING_SAVE') ?></button>
		</div>

		<div id="messages">
			<button id="message_close" onclick="document.getElementById('messages').hidden = true;">
				<i class="fas fa-times" aria-hidden="true"></i>
			</button>
		</div>

	</div>

	<?php
	$listHtml = "";
	// Set up draw area
	echo "<svg id=\"main_svg\"
	     xmlns=\"http://www.w3.org/2000/svg\"
         viewBox=\"0 0 $boxX $boxY\"
        width='99%'
	    >";

	echo "<rect x=\"0\" y=\"0\" width=\"$boxX\" height=\"$boxY\" fill=\"white\" />";

	echo "<text x='-0.5' y='1.2' style='font-size:0.4pt; fill:black' transform=\"rotate(-45)\">m/m</text>";

	for ($runX = 0; $runX < $boxX; $runX++)
	{
		echo "<line x1=\"$runX\" y1=\"0\" x2=\"$runX\" y2=\"$boxY\" stroke='blue' stroke-width='0.1' stroke-opacity='0.1' />";

		if ($runX > 1)
		{
			echo "<line x1=\"$runX\" y1=\"0\" x2=\"$runX\" y2=\"1\" stroke='black' stroke-width='0.1' />";
			echo "<text x='" . ($runX + 0.2) . "' y=\"0.8\" style='font-size:0.4pt; fill:black'>" . ($runX) . "</text>";
		}
	}

	for ($runY = 0; $runY < $boxY; $runY++)
	{
		echo "<line x1=\"0\" y1=\"$runY\" x2=\"$boxX\" y2=\"$runY\" stroke='blue' stroke-width='0.1' stroke-opacity='0.1' />";

		if ($runY > 1)
		{
			echo "<line x1=\"0\" y1=\"$runY\" x2=\"1\" y2=\"$runY\" stroke='black' stroke-width='0.1' />";
			echo "<text x='0.2' y='" . ($runY + 0.8) . "' style='font-size:0.4pt; fill:black'>" . ($runY) . "</text>";
		}
	}

	$svgString = '';
	// Do we have a saved setup

	if (count($event->svg) > 0)
	{
		foreach ($event->svg as $element)
		{
			echo $element;
			$svgString = $svgString . $element;
		}
	}

	// Do we have fittings
	if (isset($this->eventFittings))
	{
		$by = 0;

		// Store base x
		$bx = 0;

		// Store elements max width
		$wx = 0;

        /** @var FittingModel $fitting */
		foreach ($this->eventFittings as $fitting)
		{
			if ((bool) $fitting->needSpace) {
                if ($by + $fitting->width > $boxY) {
                    $by = 0;
                    $bx = $wx + 1;
                }

				if (strpos($svgString, 'img_' . $fitting->id) === false) {
                    echo "<svg id=\"index_$fitting->id\" class='dragMe confine' width=\"$fitting->length\" height=\"$fitting->width\" x=\"$bx\" y=\"$by\">";
					echo "<g id=\"g_index_$fitting->id\" class='rotateMe'>";

                    if ($fitting->image) {
                        echo "<image id='img_" . $fitting->id .
									"' href='" . Uri::base() . HTMLHelper::cleanImageURL($fitting->image)->url .
									"' name='fitting' class='draggable confine' width='" . $fitting->length .
									"' height='" . $fitting->width . "'/>";
                    }
                    else
                    {
                        echo "<rect id='img_" . $fitting->id .
                            "' name='fitting' class='draggable confine' x='" .
                            $bx . "' y='" . $by .
                            "' width='" . $fitting->length .
                            "' height='" . $fitting->width . "' fill='green' fill-opacity='0.5' />";
                    }
                    echo "<text class='rotator' x='" .
                        ($fitting->length / 2 - 1) .
                        "' y='" .
                        ($fitting->width + 1) .
                        "' style='font-size:0.5pt;' opacity='1.0'>&#x21BB; " . $fitting->userName . " &#x21BA;</text>";

                    echo "</g>";
                    echo "</svg>";
                }

                $wx = ($wx < $fitting->width) ? $fitting->width : $wx;
                $by = $by + $fitting->width + 2;
            }
        }
	}
	echo "</svg>";
	?>
	<input type="hidden" id="boxX" value="<?php echo $boxX; ?>" />
	<input type="hidden" id="boxY" value="<?php echo $boxY; ?>" />
    <input type="hidden" id="callContext" value="event.planing" />
	<input type="hidden" id="eventId" name="eventId" value="<?php echo $event->id; ?>" />

</div>