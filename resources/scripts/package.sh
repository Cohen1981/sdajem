config_read_file() {
    (grep -E "^${2}=" -m 1 "${1}" 2>/dev/null || echo "VAR=__UNDEFINED__") | head -n 1 | cut -d '=' -f 2-;
}

config_get() {
  # .env when started in make context. Else change to ../../.env
    val="$(config_read_file "${PWD}"/.env "${1}")";
    if [ "${val}" = "__UNDEFINED__" ]; then
        val="$(config_read_file config.cfg.defaults "${1}")";
    fi
    printf -- "%s" "${val%%[[:cntrl:]]}";
}

upper_first ()
{
    printf "$1" | cut -c1 -z | tr -d '\0' | tr [:lower:] [:upper:]
    printf "$1" | cut -c2-
}

rootFolder="${PWD}";
sources="${PWD}"/src
packageDir="${PWD}"/resources/packages

rm -rf "${packageDir}";
mkdir -p "${PWD}"/tmp "${packageDir}"

namespaceRoot="$(config_get NAMESPACE_ROOT)";
components="$(config_get COMPONENTS)";
if [ "${components}" != "__UNDEFINED__" ]; then

  tmpDir="${PWD}"/tmp/comp

  for component in $components
  do
    # create build directory
    mkdir -p "${tmpDir}"/administrator/components/com_"${component}" "${tmpDir}"/components/com_"${component}";

    # copy component to build directory
    cp -rf "${sources}"/"${namespaceRoot}"/Component/"$(upper_first "$component")"/Administrator/* "${tmpDir}"/administrator/components/com_"${component}";
    cp -rf "${sources}"/"${namespaceRoot}"/Component/"$(upper_first "$component")"/Site/* "${tmpDir}"/components/com_"${component}";
    # copy component media to build directory
    cp -rf "${sources}"/media/com_"${component}" "${tmpDir}"/media;
    # copy the component.xml
    cp -f "${sources}"/administrator/components/com_"${component}"/"${component}".xml "${tmpDir}"/;

    # make the zip
    cd "${tmpDir}" || exit;
    zip -r "${packageDir}"/"${component}".zip *

    cd "${rootFolder}" || exit;

    rm -rf "${tmpDir}"/administrator/components/com_"${component}";
    rm -rf "${tmpDir}"/components/com_"${component}";
    rm -f "${tmpDir}"/"${component}".xml
  done
fi

templates="$(config_get SITE_TEMPLATES)";
if [ "${templates}" != "__UNDEFINED__" ]; then

  tmpDir="${PWD}"/tmp/tmpl

  for template in $templates
  do
    # create build directory
    mkdir -p "${tmpDir}"/"${template}";

    cp -rf "${sources}"/templates/${template}/* "${tmpDir}";
    cp -rf "${sources}"/media/templates/site/${template} ./temp/template/media

    # make the zip
    cd "${tmpDir}" || exit;
    zip -r "${packageDir}"/"${template}".zip *

    cd "${rootFolder}" || exit;

    rm -rf "${tmpDir}"/"${template}";
  done
fi

rm -rf "${PWD}"/tmp
