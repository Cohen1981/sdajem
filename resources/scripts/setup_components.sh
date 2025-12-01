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

JOOMLA_FOLDER="$(config_get JOOMLA_FOLDER)"

while [ ! -f "${PWD}"/"${JOOMLA_FOLDER}"/web.config.txt ] ;
do
    echo "Waiting for file synchronization !";
    sleep 2;
done

# Waiting for auto install of joomla
while [ -d "${PWD}"/"${JOOMLA_FOLDER}"/installation ]
do
  echo "Waiting for joomla installation to complete";
  sleep 2;
done

namespaceRoot="$(config_get NAMESPACE_ROOT)";
components="$(config_get COMPONENTS)";
if [ "${components}" != "__UNDEFINED__" ]; then
  for component in $components
  do
    mkdir -v -p "${PWD}"/src/"${namespaceRoot}"/Component/"$(upper_first "$component")"/Administrator;
    mkdir -v -p "${PWD}"/src/"${namespaceRoot}"/Component/"$(upper_first "$component")"/Site;
    mkdir -v -p "${PWD}"/src/media/com_"${component}"

    linkTest="${PWD}"/"${JOOMLA_FOLDER}"/administrator/components/com_"${component}"

    if [ ! -L "${linkTest}" ] && [ ! -e "${linkTest}" ] ;
      then
        ln -sr "${PWD}"/src/"${namespaceRoot}"/Component/"$(upper_first "$component")"/Administrator "${PWD}"/"${JOOMLA_FOLDER}"/administrator/components/com_"${component}";
        ln -sr "${PWD}"/src/"${namespaceRoot}"/Component/"$(upper_first "$component")"/Site "${PWD}"/"${JOOMLA_FOLDER}"/components/com_"${component}";
        ln -sr "${PWD}"/src/media/com_"${component}" "${PWD}"/"${JOOMLA_FOLDER}"/media/com_"${component}";
    fi
  done
fi

modules="$(config_get MODULES)";
if [ "${modules}" != "__UNDEFINED__" ]; then
  for module in $modules
  do
    mkdir -v -p "${PWD}"/src/"${namespaceRoot}"/Module/"$(upper_first "$module")";

    linkTest="${PWD}"/"${JOOMLA_FOLDER}"/modules/mod_"${module}"

    if [ ! -L "${linkTest}" ] && [ ! -e "${linkTest}" ] ; then
        ln -sr "${PWD}"/src/"${namespaceRoot}"/Module/"$(upper_first "$module")" "${PWD}"/"${JOOMLA_FOLDER}"/modules/mod_"${module}";
    fi

  done
fi

#SITE_TEMPLATES
templates="$(config_get SITE_TEMPLATES)";
if [ "${templates}" != "__UNDEFINED__" ]; then
  for template in $templates
  do
    mkdir -v -p "${PWD}"/src/templates/"${template}";
    mkdir -v -p "${PWD}"/src/media/templates/site/"${template}";

     if ! [ -L "${PWD}"/"${JOOMLA_FOLDER}"/templates/"${template}" ] ;
          then
            ln -sr "${PWD}"/src/templates/"${template}" "${PWD}"/"${JOOMLA_FOLDER}"/templates/"${template}";
            ln -sr "${PWD}"/src/media/templates/site/"${template}" "${PWD}"/"${JOOMLA_FOLDER}"/media/templates/site/"${template}";
      fi

  done
fi

#ADMIN_TEMPLATES
templates="$(config_get ADMIN_TEMPLATES)";
if [ "${templates}" != "__UNDEFINED__" ]; then
  for template in $templates
  do
    mkdir -v -p "${PWD}"/src/templates/"${template}";
    mkdir -v -p "${PWD}"/src/media/templates/administrator/"${template}";

     if ! [ -L "${PWD}"/"${JOOMLA_FOLDER}"/templates/"${template}" ] ;
          then
            ln -sr "${PWD}"/src/templates/"${template}" "${PWD}"/"${JOOMLA_FOLDER}"/templates/"${template}";
            ln -sr "${PWD}"/src/media/templates/administrator/"${template}" "${PWD}"/"${JOOMLA_FOLDER}"/media/templates/administrator/"${template}";
      fi

  done
fi

#PLUGINS
